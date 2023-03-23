<?php

namespace App\Http\Controllers\Member;

use App\ClientDetails;
use App\Currency;
use App\Estimate;
use App\EstimateItem;
use App\Helper\Reply;
use App\Http\Controllers\Member\MemberBaseController;
use App\Http\Requests\StoreEstimate;
use App\Notifications\NewEstimate;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Tax;
use App\Product;
use App\InvoiceSetting;

class MemberEstimatesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.estimates';
        $this->pageIcon = 'ti-file';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('estimates', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        abort_if(!$this->user->cans('view_estimates'), 403);
        return view('member.estimates.index', $this->data);
    }

    public function create()
    {
        abort_if(!$this->user->cans('add_estimates'), 403);
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->lastEstimate = Estimate::lastEstimateNumber() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->zero = '';
        if (strlen($this->lastEstimate) < $this->invoiceSetting->estimate_digit) {
            for ($i = 0; $i < $this->invoiceSetting->estimate_digit - strlen($this->lastEstimate); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        $this->taxes = Tax::all();
        $this->products = Product::select('id', 'name as title', 'name as text')->get();

        $estimate = new Estimate();
        $this->fields = $estimate->getCustomFieldGroupsWithFields()->fields;

        return view('member.estimates.create', $this->data);
    }

    public function store(StoreEstimate $request)
    {
        $items = $request->input('item_name');
        $itemsSummary = $request->input('item_summary');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $hsnSacCode = request()->input('hsn_sac_code');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }
        $estimate = new Estimate();
        $estimate->client_id = $request->client_id;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->note = $request->note;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->status = 'waiting';
        $lastEstimate = Estimate::count();
        $estimate->estimate_number = $lastEstimate + 1;

        $estimate->status = 'waiting';
        $estimate->save();

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $estimate->updateCustomFieldData($request->get('custom_fields_data'));
        }

        foreach ($items as $key => $item) :
            if (!is_null($item)) {
                EstimateItem::create(
                    [
                        'estimate_id' => $estimate->id,
                        'item_name' => $item,
                        'item_summary' => $itemsSummary[$key],
                        'hsn_sac_code' => (isset($hsnSacCode[$key]) && !is_null($hsnSacCode[$key])) ? $hsnSacCode[$key] : null,
                        'type' => 'item',
                        'quantity' => $quantity[$key],
                        'unit_price' => round($cost_per_item[$key], 2),
                        'amount' => round($amount[$key], 2),
                        'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                    ]
                );
            }
        endforeach;

        $this->logSearchEntry($estimate->id, 'Estimate #' . $estimate->id, 'admin.estimates.edit', 'estimate');

        return Reply::redirect(route('member.estimates.index'), __('messages.estimateCreated'));
    }

    public function edit($id)
    {
        abort_if(!$this->user->cans('edit_estimates'), 403);
        $this->estimate = Estimate::findOrFail($id)->withCustomFields();
        $this->fields = $this->estimate->getCustomFieldGroupsWithFields()->fields;
        $this->clients = ClientDetails::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::select('id', 'name as title', 'name as text')->get();

        return view('member.estimates.edit', $this->data);
    }

    public function update(StoreEstimate $request, $id)
    {
        $items = $request->input('item_name');
        $cost_per_item = $request->input('cost_per_item');
        $itemsSummary = $request->input('item_summary');
        $quantity = $request->input('quantity');
        $hsnSacCode = request()->input('hsn_sac_code');
        $amount = $request->input('amount');
        $type = $request->input('type');
        $tax = $request->input('taxes');

        if (trim($items[0]) == '' || trim($items[0]) == '' || trim($cost_per_item[0]) == '') {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        foreach ($items as $itm) {
            if (is_null($itm)) {
                return Reply::error(__('messages.itemBlank'));
            }
        }


        $estimate = Estimate::findOrFail($id);
        $estimate->client_id = $request->client_id;
        $estimate->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $estimate->sub_total = round($request->sub_total, 2);
        $estimate->total = round($request->total, 2);
        $estimate->currency_id = $request->currency_id;
        $estimate->discount = round($request->discount_value, 2);
        $estimate->discount_type = $request->discount_type;
        $estimate->status = $request->status;
        $estimate->note = $request->note;
        $estimate->save();

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $estimate->updateCustomFieldData($request->get('custom_fields_data'));
        }

        // delete and create new
        EstimateItem::where('estimate_id', $estimate->id)->delete();

        foreach ($items as $key => $item) :
            EstimateItem::create(
                [
                    'estimate_id' => $estimate->id,
                    'item_name' => $item,
                    'item_summary' => $itemsSummary[$key],
                    'hsn_sac_code' => (isset($hsnSacCode[$key]) && !is_null($hsnSacCode[$key])) ? $hsnSacCode[$key] : null,
                    'type' => 'item',
                    'quantity' => $quantity[$key],
                    'unit_price' => round($cost_per_item[$key], 2),
                    'amount' => round($amount[$key], 2),
                    'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                ]
            );
        endforeach;

        return Reply::redirect(route('member.estimates.index'), __('messages.estimateUpdated'));
    }

    public function data(Request $request)
    {
        $invoices = Estimate::join('users', 'estimates.client_id', '=', 'users.id')
            ->join('currencies', 'currencies.id', '=', 'estimates.currency_id')
            ->select('estimates.id', 'estimates.client_id', 'users.name', 'estimates.total', 'currencies.currency_symbol', 'estimates.status', 'estimates.valid_till', 'estimates.estimate_number', 'estimates.send_status');


        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d');
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d');
            $invoices = $invoices->where(DB::raw('DATE(estimates.`valid_till`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $invoices = $invoices->where('estimates.status', '=', $request->status);
        }

        $invoices = $invoices->orderBy('estimates.id', 'desc')->get();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">Action <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">';
                if ($this->user->cans('view_estimates') && $row->status != 'draft') {
                    $action .= '<li><a href="' . route('member.estimates.download', $row->id) . '" ><i class="fa fa-download"></i> '.__('app.download').'</a></li>';
                }

                $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-estimate-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
 
                if ($this->user->cans('edit_estimates') && ($row->status == 'waiting' || $row->status == 'draft')) {
                    $action .= '<li><a href="' . route('member.estimates.edit', $row->id) . '" ><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                }

                if ($this->user->cans('delete_estimates')) {
                    $action .= '<li><a class="sa-params" href="javascript:;" data-estimate-id="' . $row->id . '"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }

                if ($this->user->cans('add_invoices') && $row->status != 'draft') {
                    $action .= '<li><a href="' . route('member.all-invoices.convert-estimate', $row->id) . '" ><i class="ti-receipt"></i> ' . __('app.create') . ' ' . __('app.invoice') . '</a></li>';
                }
                $action .= '</ul></div>';

                return $action;
            })
            ->editColumn('name', function ($row) {
                return '<a href="' . route('member.clients.projects', $row->client_id) . '">' . ucwords($row->name) . '</a>';
            })
            ->editColumn('status', function ($row) {
                $status = '';
                if ($row->status == 'waiting') {
                    $status .= '<label class="label label-warning">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'draft') {
                    $status .= '<label class="label label-primary">' . strtoupper($row->status) . '</label>';
                } else if ($row->status == 'declined') {
                    $status .= '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                } else {
                    $status .= '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }

                if (!$row->send_status && $row->status != 'draft') {
                    $status .= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                return $status;
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
            ->rawColumns(['name', 'action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->make(true);
    }

    public function destroy($id)
    {
        Estimate::destroy($id);
        return Reply::success(__('messages.estimateDeleted'));
    }

    public function download($id)
    {

        $this->estimate = Estimate::findOrFail($id)->withCustomFields();
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
        App::setLocale($this->global->invoice_setting->locale);
        Carbon::setLocale($this->global->invoice_setting->locale);
        $this->invoice = $this->estimate;
        $this->fields = $this->estimate->getCustomFieldGroupsWithFields()->fields;

        $pdf->loadView('estimate.' . $this->estimate->activeTemplate(), $this->data);
        // $pdf->loadView('member.estimates.estimate-pdf', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = 'estimate-' . $this->estimate->id;
        //        return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }

    public function sendEstimate($id)
    {
        $estimate = Estimate::findOrFail($id);
        if(!is_null($estimate->client)){
            $estimate->client->notify(new NewEstimate($estimate));
        }

        $estimate->send_status = 1;
        if ($estimate->status == 'draft') {
            $estimate->status = 'waiting';
        }
        $estimate->save();
        return Reply::success(__('messages.updateSuccess'));
    }

}
