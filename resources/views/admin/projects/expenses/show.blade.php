@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-success btn-outline" ><i class="icon-note"></i> @lang('app.edit')</a>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">

                    @include('admin.projects.show_project_menu')

                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-xs-12" id="invoices-list-panel">
                                    <div class="white-box">

                                        <h3>@lang('app.menu.expenses')</h3>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>@lang('modules.expenses.itemName')</th>
                                                        <th>@lang('app.price')</th>
                                                        <th>@lang('modules.expenses.purchaseFrom')</th>
                                                        <th>@lang('app.menu.employees')</th>
                                                        <th>@lang('modules.expenses.purchaseDate')</th>
                                                        <th>@lang('app.status')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($project->expenses as $key=>$item)
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ ucfirst($item->item_name) }}</td>
                                                            <td>{{ $item->currency->currency_symbol.$item->price }}</td>
                                                            <td>{{ $item->purchase_from }}</td>
                                                            <td>{{ ucwords($item->user->name) }}</td>
                                                            <td>{{ $item->purchase_date->format($global->date_format) }}</td>
                                                            <td>
                                                                @if ($item->status == 'pending')
                                                                    <label class="label label-warning">{{ strtoupper($item->status) }}</label>
                                                                @elseif ($item->status == 'approved')
                                                                    <label class="label label-success">{{ strtoupper($item->status) }}</label>
                                                                @else
                                                                    <label class="label label-danger">{{ strtoupper($item->status) }}</label>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7">@lang('messages.noRecordFound')</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-lg in" id="add-invoice-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

@endsection
