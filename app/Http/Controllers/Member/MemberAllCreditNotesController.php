<?php

namespace App\Http\Controllers\Member;

use App\CreditNoteItem;
use App\Currency;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\CreditNotes\creditNoteFileStore;
use App\Http\Requests\CreditNotes\StoreCreditNotes;
use App\Http\Requests\CreditNotes\UpdateCreditNote;
use App\CreditNotes;
use App\Invoice;
use App\InvoiceSetting;
use App\Product;
use App\Project;
use App\Tax;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MemberAllCreditNotesController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.credit-note';
        $this->pageIcon = 'ti-receipt';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('invoices', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        if(!$this->user->cans('view_invoices')){
            abort(403);
        }
        $this->projects = Project::all();
        $this->clients = User::allClients();
        return view('member.credit-notes.index', $this->data);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function data(Request $request)
    {
        $creditNotes = CreditNotes::with(['project:id,project_name,client_id', 'currency:id,currency_symbol,currency_code', 'invoice'])
            ->leftJoin('invoices', 'invoices.id', 'credit_notes.invoice_id')
            ->select('credit_notes.id', 'credit_notes.project_id', 'credit_notes.invoice_id', 'credit_notes.currency_id', 'credit_notes.cn_number',
                'credit_notes.total', 'credit_notes.issue_date', 'credit_notes.status');

        if($request->startDate !== null && $request->startDate != 'null' && $request->startDate != ''){
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->format('Y-m-d');
            $creditNotes = $creditNotes->where(DB::raw('DATE(credit_notes.`issue_date`)'), '>=', $startDate);
        }

        if($request->endDate !== null && $request->endDate != 'null' && $request->endDate != ''){
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->format('Y-m-d');
            $creditNotes = $creditNotes->where(DB::raw('DATE(credit_notes.`issue_date`)'), '<=', $endDate);
        }

        if ($request->projectID != 'all' && !is_null($request->projectID)) {
            $creditNotes = $creditNotes->where('credit_notes.project_id', '=', $request->projectID);
        }

        if ($request->clientID != 'all' && !is_null($request->clientID)) {
            $creditNotes = $creditNotes->where('invoices.client_id', '=', $request->clientID);
        }

        $creditNotes = $creditNotes->orderBy('credit_notes.id', 'desc')->get();

        return DataTables::of($creditNotes)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="btn-group m-r-10">
                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-outline  dropdown-toggle waves-effect waves-light" type="button">'.__('app.action').' <span class="caret"></span></button>
                <ul role="menu" class="dropdown-menu">
                    <li><a href="' . route('member.all-credit-notes.download', $row->id) . '"><i class="fa fa-download"></i> '.__('app.download').'</a></li>';

                $action .= ' <li><a href="javascript:" data-credit-notes-id="' . $row->id . '" class="credit-notes-upload" data-toggle="modal" data-target="#creditNoteUploadModal"><i class="fa fa-upload"></i> '.__('app.upload').' </a></li>';

                $action .= '<li><a href="' . route('member.all-credit-notes.edit', $row->id) . '"><i class="fa fa-pencil"></i> '.__('app.edit').'</a></li>';

                $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-credit-notes-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> '.__('app.delete').'</a></li>
                </ul>
              </div>
              ';

                return $action;
            })
            ->editColumn('project_name', function ($row) {
                if($row->project_id){
                    return '<a href="' . route('member.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                }
                return '--';
            })
            ->editColumn('cn_number', function ($row) {
                return '<a href="' . route('member.all-credit-notes.show', $row->id) . '">' . ucfirst($row->cn_number) . '</a>';
            })
            ->editColumn('invoice_number', function ($row) {
                return $row->invoice ? ucfirst($row->invoice->invoice_number) : '--';
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;

                return '<div class="text-right">Total: '.currency_formatter($row->total, $currencySymbol).$currencyCode.'<br>Used: '.currency_formatter($row->creditAmountUsed(), $currencySymbol).$currencyCode.'<br>Remaining: '.currency_formatter($row->creditAmountRemaining(), $currencySymbol).$currencyCode.'</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->editColumn('status', function ($row) {
                if ($row->status == 'open') {
                    return '<label class="label label-success">' . strtoupper($row->status) . '</label>';
                }
                else {
                    return '<label class="label label-danger">' . strtoupper($row->status) . '</label>';
                }
            })
            ->rawColumns(['project_name', 'action', 'cn_number', 'invoice_number', 'status', 'total'])
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_id')
            ->make(true);
    }

    public function download($id)
    {
        //        header('Content-type: application/pdf');

        $this->creditNote = CreditNotes::findOrFail($id);
        $this->invoiceNumber = 0;
        if (Invoice::where('id', '=', $this->creditNote->invoice_id)->exists()) {
            $this->invoiceNumber = Invoice::select('invoice_number')->where('id', $this->creditNote->invoice_id)->first();
        }

        // Download file uploaded
        if ($this->creditNote->file != null) {
            return download_local_s3($this->creditNote, 'credit-note-files/' . $this->creditNote->file);
        }

        if ($this->creditNote->discount > 0) {
            if ($this->creditNote->discount_type == 'percent') {
                $this->creditNote = (($this->creditNote->discount / 100) * $this->creditNote->sub_total);
            } else {
                $this->discount = $this->creditNote->discount;
            }
        } else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = CreditNoteItem::whereNotNull('taxes')
            ->where('credit_note_id', $this->creditNote->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = CreditNoteItem::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = company();
        $this->company = company();

        $this->creditNoteSetting = InvoiceSetting::first();

        $pdf = app('dompdf.wrapper');

        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->creditNoteSetting->locale);
        Carbon::setLocale($this->creditNoteSetting->locale);

        $pdf->loadView('credit-notes.' . $this->creditNoteSetting->template, $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = $this->creditNote->cn_number;
        //       return $pdf->stream();
        return $pdf->download($filename . '.pdf');
    }

    public function destroy($id)
    {
        CreditNotes::destroy($id);
        return Reply::success(__('messages.creditNoteDeleted'));
    }

    public function create()
    {
        if(!$this->user->cans('add_invoices')){
            abort(403);
        }
        abort(404);
    }

    public function store(StoreCreditNotes $request)
    {
        $items = $request->input('item_name');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $hsnSacCode = request()->input('hsn_sac_code');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
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

        $creditNote = new CreditNotes();
        $creditNote->project_id = $request->project_id;
        $creditNote->cn_number = $request->cn_number;
        $creditNote->invoice_id = $request->invoice_id ? $request->invoice_id : null;
        $creditNote->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $creditNote->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $creditNote->sub_total = round($request->sub_total, 2);
        $creditNote->discount = round($request->discount_value, 2);
        $creditNote->discount_type = $request->discount_type;
        $creditNote->total = round($request->total, 2);
        $creditNote->currency_id = $request->currency_id;
        //        $creditNote->status = $request->status;
        $creditNote->recurring = $request->recurring_payment;
        $creditNote->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $creditNote->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $creditNote->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $creditNote->note = $request->note;
        $creditNote->save();

        foreach ($items as $key => $item) :
            if (!is_null($item)) {
                CreditNoteItem::create(['credit_note_id' => $creditNote->id, 'item_name' => $item, 'type' => 'item', 'quantity' => $quantity[$key], 'unit_price' => round($cost_per_item[$key], 2), 'amount' => round($amount[$key], 2), 'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null]);
            }
        endforeach;

        if ($request->invoice_id){
            $invoice = Invoice::findOrFail($request->invoice_id);
            $invoice->credit_note = 1;
            $invoice->save();
        }

        //log search
        $this->logSearchEntry($creditNote->id, 'CreditNote ' . $creditNote->cn_number, 'member.all-credit-notes.show', 'creditNote');

        return Reply::redirect(route('member.all-credit-notes.index'), __('messages.creditNoteCreated'));
    }

    public function edit($id)
    {
        if(!$this->user->cans('edit_invoices')){
            abort(403);
        }
        $this->creditNote = CreditNotes::findOrFail($id);
        $this->projects = Project::all();
        $this->currencies = Currency::all();

        //        if ($this->creditNote->status == 'paid') {
        //            abort(403);
        //        }
        $this->taxes = Tax::all();
        $this->products = Product::select('id', 'name as title', 'name as text')->get();

        return view('member.credit-notes.edit', $this->data);
    }

    public function update(UpdateCreditNote $request, $id)
    {
        $items = $request->input('item_name');
        $cost_per_item = $request->input('cost_per_item');
        $quantity = $request->input('quantity');
        $hsnSacCode = request()->input('hsn_sac_code');
        $amount = $request->input('amount');
        $tax = $request->input('taxes');

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && $qty < 1) {
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

        $creditNote = CreditNotes::findOrFail($id);

        //        if ($creditNote->status == 'paid') {
        //            return Reply::error(__('messages.invalidRequest'));
        //        }

        $creditNote->project_id = $request->project_id;
        $creditNote->issue_date = Carbon::createFromFormat($this->global->date_format, $request->issue_date)->format('Y-m-d');
        $creditNote->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        $creditNote->sub_total = round($request->sub_total, 2);
        $creditNote->discount = round($request->discount_value, 2);
        $creditNote->discount_type = $request->discount_type;
        $creditNote->total = round($request->total, 2);
        $creditNote->currency_id = $request->currency_id;
        //        $creditNote->status = $request->status;
        $creditNote->recurring = $request->recurring_payment;
        $creditNote->billing_frequency = $request->recurring_payment == 'yes' ? $request->billing_frequency : null;
        $creditNote->billing_interval = $request->recurring_payment == 'yes' ? $request->billing_interval : null;
        $creditNote->billing_cycle = $request->recurring_payment == 'yes' ? $request->billing_cycle : null;
        $creditNote->note = $request->note;
        $creditNote->save();

        // delete and create new
        CreditNoteItem::where('credit_note_id', $creditNote->id)->delete();

        foreach ($items as $key => $item) :
            CreditNoteItem::create(['credit_note_id' => $creditNote->id, 'item_name' => $item, 'type' => 'item', 'quantity' => $quantity[$key], 'unit_price' => round($cost_per_item[$key], 2), 'amount' => round($amount[$key], 2), 'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null]);
        endforeach;

        return Reply::redirect(route('member.all-credit-notes.index'), __('messages.creditNoteUpdated'));
    }

    public function show($id)
    {
        if(!$this->user->cans('view_invoices')){
            abort(403);
        }
        $this->creditNote = CreditNotes::findOrFail($id);
        $this->paidAmount = $this->creditNote->getPaidAmount();
        if ($this->creditNote->discount > 0) {
            if ($this->creditNote->discount_type == 'percent') {
                $this->discount = (($this->creditNote->discount / 100) * $this->creditNote->sub_total);
            } else {
                $this->discount = $this->creditNote->discount;
            }
        } else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = CreditNoteItem::whereNotNull('taxes')
            ->where('credit_note_id', $this->creditNote->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = CreditNoteItem::taxbyid($tax)->first();
                if ($this->tax){
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
        $this->creditNoteSetting = InvoiceSetting::first();
        
        return view('member.credit-notes.show', $this->data);
    }

    public function convertInvoice($id)
    {
        $this->invoiceId = $id;
        $this->creditNote = Invoice::with('items')->findOrFail($id);
        $this->lastCreditNote = CreditNotes::count() + 1;
        $this->creditNoteSetting = InvoiceSetting::first();
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->taxes = Tax::all();
        $this->products = Product::select('id', 'name as title', 'name as text')->get();

        $this->zero = '';
        if (strlen($this->lastCreditNote) < $this->creditNoteSetting->credit_note_digit){
            for ($i = 0; $i < $this->creditNoteSetting->credit_note_digit - strlen($this->lastCreditNote); $i++){
                $this->zero = '0'.$this->zero;
            }
        }

        $discount = $this->creditNote->items->filter(function ($value, $key) {
            return $value->type == 'discount';
        });

        $tax = $this->creditNote->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->totalDiscount = $discount->sum('amount');

        return view('member.credit-notes.convert_invoice', $this->data);
    }

    public function addItems(Request $request)
    {
        $this->items = Product::with('tax')->find($request->id);
        $exchangeRate = Currency::find($request->currencyId);

        if (!is_null($exchangeRate) && !is_null($exchangeRate->exchange_rate)) {
            if ($this->items->total_amount != '') {
                $this->items->price = floor($this->items->total_amount * $exchangeRate->exchange_rate);
            } else {
                $this->items->price = $this->items->price * $exchangeRate->exchange_rate;
            }
        } else {
            if ($this->items->total_amount != '') {
                $this->items->price = $this->items->total_amount;
            }
        }
        $this->items->price = number_format((float)$this->items->price, 2, '.', '');

        $this->taxes = Tax::all();
        $view = view('member.credit-notes.add-item', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function paymentDetail($creditNoteID)
    {
        $this->creditNote = CreditNotes::findOrFail($creditNoteID);

        return View::make('member.credit-notes.payment-detail', $this->data);
    }

    /**
     * @param creditNoteFileStore $request
     * @return array
     * @throws \Exception
     */
    public function storeFile(creditNoteFileStore $request)
    {
        $creditNoteId = $request->credit_note_id;
        $file = $request->file('file');

        // Getting invoice data
        $creditNote = CreditNotes::find($creditNoteId);

        if ($creditNote != null) {
            $creditNote->file = Files::uploadLocalOrS3($file, 'credit-note-files');;
            $creditNote->file_original_name = $file->getClientOriginalName(); // Getting uploading file name;

            $creditNote->save();

            return Reply::success(__('messages.fileUploadedSuccessfully'));
        }

        return Reply::error(__('messages.fileUploadIssue'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function destroyFile(Request $request)
    {
        $creditNoteId = $request->credit_note_id;

        $creditNote = CreditNotes::find($creditNoteId);

        if ($creditNote != null) {
            $creditNote->file = null;
            $creditNote->file_original_name = null;

            $creditNote->save();
        }

        return Reply::success(__('messages.fileDeleted'));
    }

}
