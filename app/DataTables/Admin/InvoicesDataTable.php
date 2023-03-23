<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class InvoicesDataTable extends BaseDataTable
{
    protected $firstInvoice;
    protected $invoiceSettings;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $firstInvoice = $this->firstInvoice;
        $invoiceSettings = $this->invoiceSettings;
        $taxes = Tax::all();
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->filterColumn('invoice_number', function ($query, $keyword) use ($invoiceSettings) {
                $string = ltrim(str_replace($invoiceSettings->invoice_prefix . '#', '', $keyword), '0');
                $sql = 'invoices.invoice_number  like ?';
                $query->whereRaw($sql, ["%{$string}%"]);
            })

            ->addColumn('action', function ($row) use ($firstInvoice) {
                $action = '<div class="btn-group dropdown m-r-10">
                 <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle waves-effect waves-light" type="button"><i class="fa fa-gears "></i></button>
                <ul role="menu" class="dropdown-menu">';

                if ($row->status != 'draft') {
                    $action .= '<li><a href="' . route('admin.all-invoices.download', $row->id) . '"><i class="fa fa-download"></i> ' . __('app.download') . '</a></li>';
                }

                if ($row->status != 'canceled' && ($row->client_id != null || $row->project->client_id != null)) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sendButton"><i class="fa fa-send"></i> ' . __('app.send') . '</a></li>';
                }

                if ($row->status == 'paid') {
                    $action .= ' <li><a href="javascript:" data-invoice-id="' . $row->id . '" class="invoice-upload" data-toggle="modal" data-target="#invoiceUploadModal"><i class="fa fa-upload"></i> ' . __('app.upload') . ' </a></li>';
                }

                if (($row->status != 'paid' && $row->status != 'canceled') || ( $row->status == 'paid' && $row->amountDue() > 0)) {

                    if (is_null($row->invoice_recurring_id)) {
                        $action .= '<li><a href="' . route('admin.all-invoices.edit', $row->id) . '"><i class="fa fa-pencil"></i> ' . __('app.edit') . '</a></li>';
                    }

                    if (in_array('payments', $this->user->modules) && $row->credit_note == 0 && $row->status != 'draft') {
                        $action .= '<li><a href="' . route('admin.payments.payInvoice', [$row->id]) . '" data-toggle="tooltip" ><i class="fa fa-plus"></i> ' . __('modules.payments.addPayment') . '</a></li>';
                    }
                }

                if ($row->status != 'canceled') {
                    if (isset($row->clientdetails)) {
                        if (!is_null($row->clientdetails->shipping_address)) {
                            if ($row->show_shipping_address === 'yes') {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                            } else {
                                $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                            }
                        } else {
                            $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                        }
                    } else {

                        if (isset($row->project->clientdetails)) {
                            if (!is_null($row->project->clientdetails->shipping_address)) {
                                if ($row->show_shipping_address === 'yes') {
                                    $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye-slash"></i> ' . __('app.hideShippingAddress') . '</a></li>';
                                } else {
                                    $action .= '<li><a href="javascript:toggleShippingAddress(' . $row->id . ');"><i class="fa fa-eye"></i> ' . __('app.showShippingAddress') . '</a></li>';
                                }
                            } else {
                                $action .= '<li><a href="javascript:addShippingAddress(' . $row->id . ');"><i class="fa fa-plus"></i> ' . __('app.addShippingAddress') . '</a></li>';
                            }
                        }
                    }
                }
                if ($firstInvoice->id == $row->id && is_null($row->invoice_recurring_id)) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-params"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }
                else{
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-params-notice"><i class="fa fa-times"></i> ' . __('app.delete') . '</a></li>';
                }

                if ($firstInvoice->id != $row->id && ($row->status == 'unpaid' || $row->status == 'draft')) {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-cancel"><i class="fa fa-times"></i> ' . __('modules.invoices.markCancel') . '</a></li>';
                }
                else{
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="sa-cancel-check"><i class="fa fa-times"></i> ' . __('modules.invoices.markCancel') . '</a></li>';
                }

                if ($row->status != 'paid' && $row->credit_note == 0 && $row->status != 'draft' && $row->status != 'canceled') {
                    $action .= '<li><a href="' . route('front.invoice', [md5($row->id)]) . '" target="_blank" data-toggle="tooltip" ><i class="fa fa-link"></i> ' . __('modules.payments.paymentLink') . '</a></li>';
                }
                if ($row->credit_note == 0 && $row->status != 'draft' && $row->status != 'canceled') {
                    if ($row->status == 'paid') {
                        $action .= '<li><a href="' . route('admin.all-credit-notes.convert-invoice', $row->id) . '" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="addCreditNote"><i class="fa fa-plus"></i> ' . __('modules.credit-notes.addCreditNote') . '</a></li>';
                    } else {
                        $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="unpaidAndPartialPaidCreditNote"><i class="fa fa-plus"></i> ' . __('modules.credit-notes.addCreditNote') . '</a></li>';
                    }
                }

                if (!is_null($row->clientdetails)) {
                    if (isset($row->clientdetails['user']) && $row->clientdetails['user']['status'] == 'active') {
                        if ($row->status != 'paid' && $row->status != 'draft' && $row->status != 'canceled') {
                            $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="reminderButton"><i class="fa fa-money"></i> ' . __('app.paymentReminder') . '</a></li>';
                        }
                    }
                } else {
                    if (isset($row->project->clientdetails)) {
                        if ($row->project->clientdetails->user->status == 'active') {
                            if ($row->status != 'paid' && $row->status != 'draft' && $row->status != 'canceled') {
                                $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="reminderButton"><i class="fa fa-money"></i> ' . __('app.paymentReminder') . '</a></li>';
                            }
                        }
                    }
                }

                if ($row->status == 'review') {
                    $action .= '<li><a href="javascript:;" data-toggle="tooltip"  data-invoice-id="' . $row->id . '" class="verify"><i class="fa fa-check"></i> ' . __('app.verify') . '</a></li>';
                }

                $action .= '</ul> </div>';

                return $action;
            })
            ->editColumn('project_name', function ($row) {
                if ($row->project_id != null) {
                    if ($row->project && $row->project->deleted_at == null) {
                        return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project->project_name) . '</a>';
                    }
                }
                return '--';
            })
            ->editColumn('name', function ($row) {
                if ($row->client_id != '') {

                    if ($row->name) {
                        return ucfirst($row->name);
                    }

                    return '--';
                }
                if ($row->project && $row->project->client) {
                    return ucfirst($row->project->client->name);
                }
                if ($row->estimate && $row->estimate->client) {
                    return ucfirst($row->estimate->client->name);
                }
                return '--';
            })
            ->editColumn('invoice_number', function ($row) {
                if (is_null($row->invoice_recurring_id)) {
                    return '<a href="' . route('admin.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a>';
                }
                return '<a href="' . route('admin.all-invoices.show', $row->id) . '">' . ucfirst($row->invoice_number) . '</a> <br> <label class="label label-info"> ' . __('app.recurring') . ' </label>';
            })
            ->editColumn('status', function ($row) {
                $status = '';
                if ($row->credit_note) {
                    $status .= '<label class="label label-warning">' . strtoupper(__('app.credit-note')) . '</label>';
                } else {
                    if ($row->status == 'unpaid') {
                        $status .= '<label class="label label-danger">' . __('app.' . $row->status) . '</label>';
                    } elseif ($row->status == 'paid') {
                        $status .= '<label class="label label-success">' . __('app.' . $row->status) . '</label>';
                    } elseif ($row->status == 'draft') {
                        $status .= '<label class="label label-primary">' . __('app.' . $row->status) . '</label>';
                    } elseif ($row->status == 'canceled') {
                        $status .= '<label class="label label-danger">' . __('app.' . $row->status) . '</label>';
                    } elseif ($row->status == 'review') {
                        return '<label class="label label-warning">' . __('app.' . $row->status) . '</label>';
                    } else {
                        $status .= '<label class="label label-info">' . strtoupper(__('modules.invoices.partial')) . '</label>';
                    }
                }
                if (!$row->send_status && $row->status != 'draft') {
                    $status .= '<br><br><label class="label label-inverse">' . strtoupper(__('modules.invoices.notSent')) . '</label>';
                }
                return $status;
            })
            ->editColumn('total', function ($row) {
                $currencyCode = ' (' . $row->currency->currency_code . ') ';
                $currencySymbol = $row->currency->currency_symbol;

                return '<div class="text-right">' . __('app.total') . ': ' . currency_formatter($row->total, $currencySymbol) . '<br><span class="text-success">' . __('app.paid') . ':</span> ' . currency_formatter($row->amountPaid(), $currencySymbol)  . '<br><span class="text-danger">' . __('app.unpaid') . ':</span> ' . currency_formatter($row->amountDue(), $currencySymbol) . '</div>';
            })
            ->editColumn(
                'issue_date',
                function ($row) {
                    return $row->issue_date->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->editColumn(
                'tax',
                function ($row) use ($taxes){

                    $taxList = '';
                    $taxTotal = 0;
                        $items = $row->items->filter(function ($value, $key) {
                            return $value->taxes !== null;
                        })->all();

                    foreach ($items as $item) {
                        foreach (json_decode($item->taxes) as $tax) {
                            $this->tax = $taxes->filter(function ($value, $key) use ($tax) {
                                return $value->id == $tax;
                            })->first();
                            if (!is_null($this->tax) && isset($this->tax->tax_name)){
                                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                                    $taxList .= ' '.$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'.' '. ($this->tax->rate_percent / 100) * $item->amount.'<br>';
                                    $taxTotal +=  ($this->tax->rate_percent / 100) * $item->amount;

                                } else {
                                    $taxList .= $this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'.'</br> ';
                                }
                            }

                        }
                    }
                    if($taxTotal == 0){
                        return '--';
                    }

                    return $taxList .' '. __('app.total').' : '.$taxTotal;
                }
            )
            ->rawColumns(['project_name', 'action', 'status','tax', 'invoice_number', 'total'])
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code')
            ->removeColumn('project_id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
        $request = $this->request();

        $this->firstInvoice = Invoice::orderBy('id', 'desc')->first();
        $this->invoiceSettings = InvoiceSetting::select('invoice_prefix', 'invoice_digit')->first();

        $model = $model->with(['project' => function ($q) {
            $q->withTrashed();
            $q->select('id', 'project_name', 'client_id', 'deleted_at');
        }, 'currency:id,currency_symbol,currency_code', 'project.client', 'client'])
            ->select('invoices.id', 'invoices.project_id', 'invoices.client_id', 'invoices.invoice_number', 'invoices.currency_id', 'invoices.total', 'invoices.status', 'invoices.issue_date', 'client_details.company_name', 'invoices.credit_note', 'invoices.show_shipping_address', 'invoices.send_status', 'invoices.invoice_recurring_id', 'users.name')

            ->leftJoin('users', 'invoices.client_id', 'users.id')
            ->leftJoin('client_details', 'client_details.user_id', 'users.id');


        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(invoices.`issue_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(invoices.`issue_date`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $model = $model->where('invoices.status', '=', $request->status);
        }

        if ($request->projectID != 'all' && !is_null($request->projectID)) {
            $model = $model->where('invoices.project_id', '=', $request->projectID);
        }

        if ($request->clientID != 'all' && !is_null($request->clientID)) {
            $model = $model->where('client_id', '=', $request->clientID);
        }

        $model = $model->whereHas('project', function ($q) {
            $q->whereNull('deleted_at');
        }, '>=', 0);
        $model = $model->groupBy('invoices.id');
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('invoices-table')
            ->columns($this->processTitle($this->getColumns()))
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__('app.datatable'))
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["invoices-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $modules = $this->user->modules;
        $dsData = [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('modules.client.companyName') => ['data' => 'company_name', 'name' => 'client_details.company_name','visible' => false],
            __('app.invoice') . '#' => ['data' => 'invoice_number', 'name' => 'invoice_number'],
            __('app.client') => ['data' => 'name', 'name' => 'users.name'],
            __('modules.invoices.tax') => ['data' => 'tax', 'name' => 'tax','visible' => false,'searchable' => false],
            __('modules.invoices.total') => ['data' => 'total', 'name' => 'total'],
            __('modules.invoices.invoiceDate') => ['data' => 'issue_date', 'name' => 'issue_date'],
            __('app.status') => ['data' => 'status', 'name' => 'status'],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];
        if (in_array('projects', $modules)) {
            $dsData = array_slice($dsData, 0, 3, true) + [__('app.project')  => ['data' => 'project_name', 'name' => 'project.project_name']] + array_slice($dsData, 3, count($dsData) - 1, true);
        }

        return $dsData;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Invoices_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }

}