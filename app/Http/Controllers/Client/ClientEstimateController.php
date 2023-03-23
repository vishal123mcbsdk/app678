<?php

namespace App\Http\Controllers\Client;

use App\Estimate;
use App\EstimateItem;
use App\InvoiceSetting;
use App\ModuleSetting;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;

class ClientEstimateController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'icon-doc';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('estimates', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        return view('client.estimates.index', $this->data);
    }

    public function create()
    {
        $invoices = Estimate::join('currencies', 'currencies.id', '=', 'estimates.currency_id')
            ->select('estimates.id', 'estimates.client_id', 'estimates.total', 'currencies.currency_symbol', 'estimates.status', 'estimates.valid_till', 'estimates.estimate_number')
            ->where('estimates.client_id', $this->user->id)
            ->orderBy('estimates.id', 'desc')
            ->where('estimates.status', '<>', 'draft')
            ->where('estimates.status', '<>', 'canceled')
            ->where('estimates.send_status', 1)
            ->get();

        return DataTables::of($invoices)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('front.estimate.show', md5($row->id)) . '" class="btn btn-info btn-outline" target="_blank"><i class="fa fa-eye"></i></a> <a href="' . route('client.estimates.download', $row->id) . '" data-toggle="tooltip" data-original-title="Download" class="btn btn-inverse btn-outline"><i class="fa fa-download"></i></a>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'waiting') {
                    return '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                }
                if ($row->status == 'declined') {
                    return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                } else {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
            })
            ->editColumn('total', function ($row) {
                return currency_formatter($row->total, $row->currency_symbol);
            })
            ->editColumn(
                'valid_till',
                function ($row) {
                    return Carbon::parse($row->valid_till)->format($this->global->date_format);
                }
            )
            ->rawColumns(['action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->make(true);
    }

    public function download($id)
    {
        $this->invoiceSetting = InvoiceSetting::first();
        $this->estimate = Estimate::with('items')->findOrFail($id)->withCustomFields();
        if ($this->estimate->discount > 0) {
            if ($this->estimate->discount_type == 'percent') {
                $this->discount = (($this->estimate->discount / 100) * $this->estimate->sub_total);
            } else {
                $this->discount = $this->estimate->discount;
            }
        } else {
            $this->discount = 0;
        }
        $taxList = array();

        $items = EstimateItem::whereNotNull('taxes')
            ->where('estimate_id', $this->estimate->id)
            ->get();

        foreach ($items as $item) {
            if ($this->estimate->discount > 0 && $this->estimate->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->estimate->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();
                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $this->fields = $this->estimate->getCustomFieldGroupsWithFields()->fields;

        $pdf->loadView('estimate.' . $this->estimate->activeTemplate(), $this->data);
        // $pdf->loadView('client.estimates.estimate-pdf', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = 'estimate-' . $this->estimate->id;
            //    return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }

}
