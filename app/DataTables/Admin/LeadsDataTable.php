<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Lead;
use App\LeadStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class LeadsDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $currentDate = Carbon::today()->format('Y-m-d');
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {

                if ($row->client_id == null || $row->client_id == '') {
                    $follow = '<li><a href="' . route('admin.clients.create') . '/' . $row->id . '"><i class="fa fa-user"></i> ' . __('modules.lead.changeToClient') . '</a></li>';
                    if ($row->next_follow_up == 'yes') {
                        $follow .= '<li onclick="followUp(' . $row->id . ')"><a href="javascript:;"><i class="fa fa-thumbs-up"></i> ' . __('modules.lead.addFollowUp') . '</a></li>';
                    }
                } else {
                    $follow = '';
                }
                $action = '<div class="btn-group dropdown m-r-10">
                 <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default dropdown-toggle waves-effect waves-light" type="button"><i class="fa fa-gears "></i></button>
                <ul role="menu" class="dropdown-menu pull-right">
                    <li><a href="' . route('admin.leads.show', $row->id) . '"><i class="fa fa-search"></i> ' . __('modules.lead.view') . '</a></li>
                    <li><a href="' . route('admin.leads.edit', $row->id) . '"><i class="fa fa-edit"></i> ' . __('modules.lead.edit') . '</a></li>
                    <li><a href="javascript:;" class="sa-params" data-user-id="' . $row->id . '"><i class="fa fa-trash "></i> ' . __('app.delete') . '</a></li>
                     ' . $follow . '   
                </ul>
              </div>';
                return $action;
            })
            ->addColumn('status', function ($row) {
                $status = LeadStatus::all();
                $statusLi = '--';
                foreach ($status as $st) {
                    if ($row->status_id == $st->id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $statusLi .= '<option ' . $selected . ' value="' . $st->id . '">' . $st->type . '</option>';
                }

                $action = '<select class="form-control statusChange" name="statusChange" onchange="changeStatus( ' . $row->id . ', this.value)">
                    ' . $statusLi . '
                </select>';


                return $action;
            })
            ->addColumn('leadStatus', function ($row) {
                $status = LeadStatus::all();
                $leadStatus = '';
                foreach ($status as $st) {
                    if ($row->status_id == $st->id) {
                        $leadStatus = $st->type;
                    }
                }

                return $leadStatus;
            })
            ->editColumn('client_name', function ($row) {
                if ($row->client_id != null && $row->client_id != '') {
                    $label = '<label class="label label-success">' . __('app.client') . '</label>';
                } else {
                    $label = '<label class="label label-info">' . __('app.lead') . '</label>';
                }

                return '<a href="' . route('admin.leads.show', $row->id) . '">' . ucwords($row->client_name) . '</a><div class="clearfix"></div> ' . $label;
            })
            ->editColumn('next_follow_up_date', function ($row) use ($currentDate) {
                if ($row->next_follow_up_date != null && $row->next_follow_up_date != '') {
                    $date = Carbon::parse($row->next_follow_up_date)->format($this->global->date_format .' '.$this->global->time_format);
                } else {
                    $date = '--';
                }
                if ($row->pending_follow_up < $currentDate && !is_null($row->pending_follow_up)) {
                    $date = Carbon::parse($row->pending_follow_up)->format($this->global->date_format .' '.$this->global->time_format);
                    return $date . ' <label class="label label-danger">' . __('app.pending') . '</label>';
                }

                return $date;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format($this->global->date_format);
            })
            ->editColumn('agent_name', function ($row) {
                if (!is_null($row->agent_name)) {
                    return ($row->image) ? '<img src="' . asset_url('avatar/' . $row->image) . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->agent_name) : '<img src="' . asset('img/default-profile-3.png') . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->agent_name);
                }
                return '--';
            })
            ->editColumn('client_email', function ($row) {
                if ($row->client_email != null && $row->client_email != '') {
                    return ($row->client_email);
                } else {
                    return '--';
                }
            })
            ->editColumn('mobile', function ($row) {
                if(!is_null($row->mobile) && $row->mobile != ' ')
                    {
                        return '<a href="tel:+'. ($row->mobile) . '">'.'+'.($row->mobile) .'</a>';
                }
                    return '--';

            })
            ->removeColumn('status_id')
            ->removeColumn('client_id')
            ->removeColumn('lead_value')
            ->removeColumn('source')
            ->removeColumn('next_follow_up')
            ->removeColumn('statusName')
            ->addIndexColumn()
            ->rawColumns(['status', 'action', 'client_name', 'next_follow_up_date', 'agent_name','mobile','client_email']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $setting = company();
        $currentDate = Carbon::now()->timezone($setting->timezone)->format('Y-m-d H:i');
        $lead = Lead::select('leads.id', 'leads.client_id', 'leads.mobile', 'leads.client_email', 'leads.next_follow_up', 'client_name', 'company_name', 'lead_status.type as statusName', 'status_id', 'leads.created_at', 'leads.value', 'lead_sources.type as source', 'users.name as agent_name', 'users.image',
            \DB::raw("(select next_follow_up_date from lead_follow_up where lead_id = leads.id and leads.next_follow_up  = 'yes' and next_follow_up_date >= '{$currentDate}' ORDER BY next_follow_up_date asc limit 1) as next_follow_up_date"),\DB::raw("(select follow.next_follow_up_date as pending_follow_up from lead_follow_up as follow where follow.lead_id = leads.id and leads.next_follow_up  = 'yes' ORDER BY next_follow_up_date desc limit 1) as pending_follow_up"))
            ->leftJoin('lead_status', 'lead_status.id', 'leads.status_id')
            ->leftJoin('lead_agents', 'lead_agents.id', 'leads.agent_id')
            ->leftJoin('users', 'users.id', 'lead_agents.user_id')
            ->leftJoin('lead_sources', 'lead_sources.id', 'leads.source_id');
        if ($this->request()->followUp != 'all' && $this->request()->followUp != '') {
             $lead = $lead->having('pending_follow_up', '<', $currentDate);
        }
        else{
            $lead = $lead->leftJoin('lead_follow_up', 'leads.id', 'lead_follow_up.lead_id');
        }
        if ($this->request()->client != 'all' && $this->request()->client != '') {
            if ($this->request()->client == 'lead') {
                $lead = $lead->whereNull('client_id');
            } else {
                $lead = $lead->whereNotNull('client_id');
            }
        }
        if ($this->request()->startDate !== null && $this->request()->startDate != 'null' && $this->request()->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $this->request()->startDate)->toDateString();
            $lead = $lead->where(DB::raw('DATE(lead_follow_up.`next_follow_up_date`)'), '>=', $startDate);
        }

        if ($this->request()->endDate !== null && $this->request()->endDate != 'null' && $this->request()->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $this->request()->endDate)->toDateString();
            $lead = $lead->where(DB::raw('DATE(lead_follow_up.`next_follow_up_date`)'), '<=', $endDate);
        }

        if ($this->request()->agent != 'all' && $this->request()->agent != '') {
            $lead = $lead->where('agent_id', $this->request()->agent);
        }
        if ($this->request()->category_id != 'all' && $this->request()->category_id != '') {
            $lead = $lead->where('category_id', $this->request()->category_id);
        }
        if ($this->request()->source_id != 'all' && $this->request()->source_id != '') {
            $lead = $lead->where('source_id', $this->request()->source_id);
        }

        return $lead->groupBy('leads.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('leads-table')
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
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["leads-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                    $(".statusChange").selectpicker();
                }',
            ])
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('app.clientName') => ['data' => 'client_name', 'name' => 'client_name'],
            __('modules.lead.companyName') => ['data' => 'company_name', 'name' => 'company_name'],
            __('app.leadValue') => ['data' => 'value', 'name' => 'value'],
            __('app.createdOn') => ['data' => 'created_at', 'name' => 'created_at'],
            __('modules.lead.nextFollowUp') => ['data' => 'next_follow_up_date', 'name' => 'next_follow_up_date', 'orderable' => true, 'searchable' => true],
            __('modules.lead.leadAgent') => ['data' => 'agent_name', 'name' => 'users.name'],
            __('modules.lead.client_email') => ['data' => 'client_email', 'name' => 'client_email'],
            __('app.mobile') => ['data' => 'mobile', 'name' => 'mobile'],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'exportable' => false],
            __('app.leadStatus') => ['data' => 'leadStatus', 'name' => 'leadStatus', 'visible' => false, 'orderable' => false, 'searchable' => false],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'leads_' . date('YmdHis');
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
