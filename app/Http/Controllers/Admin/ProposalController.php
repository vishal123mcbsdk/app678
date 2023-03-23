<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Events\NewProposalEvent;
use App\Helper\Reply;
use App\Http\Requests\Proposal\StoreRequest;
use App\Invoice;
use App\InvoiceSetting;
use App\Lead;
use App\Proposal;
use App\ProposalItem;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;

class ProposalController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-user';
        $this->pageTitle = 'modules.lead.proposal';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('leads', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function Index()
    {
        $this->totalProposals = Proposal::count();
        return view('admin.proposals.index', $this->data);
    }

    public function show($id)
    {
        $this->lead = Lead::where('id', $id)->first();
        return view('admin.proposals.show', $this->data);
    }

    public function data($id = null)
    {
        $lead = Proposal::select('proposals.id', 'leads.client_name', 'total', 'valid_till', 'proposals.status', 'proposals.send_status', 'currencies.currency_symbol')
            ->join('currencies', 'currencies.id', '=', 'proposals.currency_id')
            ->join('leads', 'leads.id', 'proposals.lead_id');

        if ($id) {
            $lead = $lead->where('proposals.lead_id', $id);
        }
        $lead = $lead->get();

        return DataTables::of($lead)
            ->addColumn('action', function ($row) {
                $convert = '';
                $status = '';

                if ($row->send_status == 0) {
                    $status = '<li><a class="send-mail" href="javascript:;" data-proposal-id="' . $row->id . '"  ><i class="fa fa-send"></i> ' . __('modules.proposal.sendProposal') . ' </a></li>';
                }
                if ($row->status == 'waiting' || $row->status == 'draft') {
                    $convert = '<li><a href="' . route('admin.proposals.convert-proposal', $row->id) . '" ><i class="fa fa-file"></i> Convert Invoice </a></li>
                                <li><a href="' . route('admin.proposals.edit', $row->id) . '" ><i class="fa fa-pencil"></i> ' . __('modules.proposal.edit') . ' </a></li>';
                }
                return '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">' . __('modules.proposal.action') . ' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">
                  <li><a href="' . route('admin.proposals.download', $row->id) . '" ><i class="fa fa-download"></i> ' . __('modules.proposal.download') . '</a></li>
                  <li><a target="_blank" href="' . route('front.proposal', md5($row->id)) . '" ><i class="fa fa-link"></i> ' . __('modules.proposal.publicLink') . '</a></li>
                 
                  ' . $convert . '
                  ' . $status . '
                  <li><a class="sa-params" href="javascript:;" data-proposal-id="' . $row->id . '"><i class="fa fa-times"></i> ' . __('modules.proposal.delete') . ' </a></li>
                </ul>
              </div>
              ';
            })
            ->editColumn('client_name', function ($row) {
                if ($row->client_id) {
                    return '<a href="' . route('admin.clients.projects', $row->client_id) . '">' . ucwords($row->client_name) . '</a>';
                }
                return ucwords($row->client_name);
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'waiting') {
                    return '<label class="label label-warning">' . __('app.waiting')  . '</label>';
                }
                if ($row->status == 'draft') {
                    return '<label class="label label-primary">' . __('app.draft')  . '</label>';
                }
                if ($row->status == 'declined') {
                    return '<label class="label label-danger">' . __('app.declined') . '</label>';
                } else {
                    return '<label class="label label-success">' . __('app.accepted') . '</label>';
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
            ->rawColumns(['name', 'action', 'status'])
            ->removeColumn('currency_symbol')
            ->removeColumn('client_id')
            ->make(true);
    }

    public function create($leadID = null)
    {
        $this->leads = Lead::all();
        $this->taxes = Tax::all();
        if ($leadID) {
            $this->lead = Lead::findOrFail($leadID);
        }

        $this->currencies = Currency::all();
        return view('admin.proposals.create', $this->data);
    }

    public function sendProposal($id)
    {
        $proposal = Proposal::findOrFail($id);
        $proposal->send_status = 1;
        $proposal->save();
        $type = 'new';
        event(new NewProposalEvent($proposal, $type));
        return Reply::success( __('messages.proposalSendSuccess'));
    }

    public function store(StoreRequest $request)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $hsnSacCode = $request->hsn_sac_code;
        $amount = $request->amount;
        $itemsSummary = $request->input('item_summary');
        $tax = $request->input('taxes');
        $type = $request->type;

        if($items) {
            foreach ($items as $index => $item) {
                if (!is_null($item)) {
                    if (!is_numeric($quantity[$index]) && (intval($quantity[$index]) < 1)) {
                        return Reply::error(__('messages.quantityNumber'));
                    }
                    if (!is_numeric($cost_per_item[$index])) {
                        return Reply::error(__('messages.unitPriceNumber'));
                    }
                    if (!is_numeric($amount[$index])) {
                        return Reply::error(__('messages.amountNumber'));
                    }
                }

                if ($index > 0 && is_null($item)) {
                    return Reply::error(__('messages.itemBlank'));
                }

            }
        }

        $proposal = new Proposal();
        $proposal->lead_id = $request->lead_id;
        $proposal->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $proposal->sub_total = $request->sub_total;
        $proposal->total = $request->total;
        $proposal->currency_id = $request->currency_id;
        $proposal->note = $request->note;
        $proposal->description = $request->description;
        $proposal->discount = round($request->discount_value, 2);
        $proposal->discount_type = $request->discount_type;
        $proposal->status = 'waiting';
        $proposal->signature_approval = ($request->require_signature) ? 1 : 0;
        $proposal->save();

        if($items)
        {
            foreach ($items as $key => $item) :
                if (!is_null($item)) {
                    ProposalItem::create(
                        [
                            'proposal_id' => $proposal->id,
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
        }

        $this->logSearchEntry($proposal->id, 'Proposal #' . $proposal->id, 'admin.proposals.edit', 'proposal');

        return Reply::redirect(route('admin.proposals.show', $proposal->lead_id), __('messages.proposalCreated'));
    }

    public function edit($id)
    {
        $this->Leads = Lead::all();
        $this->currencies = Currency::all();
        $this->perposal = Proposal::findOrFail($id);
        $this->taxes = Tax::all();
        return view('admin.proposals.edit', $this->data);
    }

    public function update(StoreRequest $request, $id)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $hsnSacCode = $request->hsn_sac_code;
        $amount = $request->amount;
        $type = $request->type;
        $itemsSummary = $request->input('item_summary');
        $tax = $request->input('taxes');
        if($items){
            foreach ($items as $index => $item) {
                if (!is_null($item)) {
                    if (!is_numeric($quantity[$index]) && (intval($quantity[$index]) < 1)) {
                        return Reply::error(__('messages.quantityNumber'));
                    }
                    if (!is_numeric($cost_per_item[$index])) {
                        return Reply::error(__('messages.unitPriceNumber'));
                    }
                    if (!is_numeric($amount[$index])) {
                        return Reply::error(__('messages.amountNumber'));
                    }
                }

                if ($index > 0 && is_null($item)) {
                    return Reply::error(__('messages.itemBlank'));
                }

            }
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->lead_id = $request->lead_id;
        $proposal->valid_till = Carbon::createFromFormat($this->global->date_format, $request->valid_till)->format('Y-m-d');
        $proposal->sub_total = $request->sub_total;
        $proposal->total = $request->total;
        $proposal->currency_id = $request->currency_id;
        $proposal->status = $request->status;
        $proposal->note = $request->note;
        $proposal->description = $request->description;
        $proposal->discount = round($request->discount_value, 2);
        $proposal->discount_type = $request->discount_type;
        $proposal->signature_approval = ($request->require_signature) ? 1 : 0;
        $proposal->save();

        // delete and create new
        ProposalItem::where('proposal_id', $proposal->id)->delete();
        if($items) {
            foreach ($items as $key => $item) :
                if (!is_null($item)) {
                    ProposalItem::create(
                        [
                            'proposal_id' => $proposal->id,
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
        }

        return Reply::redirect(route('admin.proposals.show', $proposal->lead_id), __('messages.proposalUpdated'));
    }

    public function destroy($id)
    {
        Proposal::destroy($id);
        return Reply::success(__('messages.proposalDeleted'));
    }

    public function download($id)
    {
        $this->proposal = Proposal::findOrFail($id);
        if ($this->proposal->discount > 0) {
            if ($this->proposal->discount_type == 'percent') {
                $this->discount = (($this->proposal->discount / 100) * $this->proposal->sub_total);
            } else {
                $this->discount = $this->proposal->discount;
            }
        } else {
            $this->discount = 0;
        }
        $this->taxes = ProposalItem::where('type', 'tax')
            ->where('proposal_id', $this->proposal->id)
            ->get();

        $items = ProposalItem::whereNotNull('taxes')
            ->where('proposal_id', $this->proposal->id)
            ->get();

        $taxList = array();

        foreach ($items as $item) {
            if ($this->proposal->discount > 0 && $this->proposal->discount_type == 'percent') {
                $item->amount = $item->amount - (($this->proposal->discount / 100) * $item->amount);
            }
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = ProposalItem::taxbyid($tax)->first();
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

        $this->settings = $this->company;

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $pdf->loadView('admin.proposals.proposal-pdfnew ', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));
        $filename = 'proposal-' . $this->proposal->id;

        return $pdf->download($filename . '.pdf');
    }

    public function convertProposal($id)
    {
        $this->proposalId = $id;
        $this->invoice = Proposal::with('items', 'lead', 'lead.client')->findOrFail($id);
        $this->lastInvoice = Invoice::lastInvoiceNumber() + 1;
        $this->invoiceSetting = InvoiceSetting::first();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->clients = User::allClients();
        $this->zero = '';

        if (!is_null($this->invoice->client_id)) {
            $this->clientDetail = User::findOrFail($this->invoice->client_id);
        }

        if (strlen($this->lastInvoice) < $this->invoiceSetting->invoice_digit) {
            for ($i = 0; $i < $this->invoiceSetting->invoice_digit - strlen($this->lastInvoice); $i++) {
                $this->zero = '0' . $this->zero;
            }
        }
        //        foreach ($this->invoice->items as $items)

        $discount = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });

        $tax = $this->invoice->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');

        return view('admin.proposals.convert_proposal', $this->data);
    }

}
