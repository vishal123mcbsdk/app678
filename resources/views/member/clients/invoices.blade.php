@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection


@section('content')

    <div class="row">


        <div class="col-xs-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-xs-6 b-r"> <strong>@lang('modules.employees.fullName')</strong> <br>
                        <p class="text-muted">{{ ucwords($client->name) }}</p>
                    </div>
                    <div class="col-xs-6"> <strong>@lang('app.mobile')</strong> <br>
                        <p class="text-muted">{{ $client->mobile ?? '-'}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-xs-6 b-r"> <strong>@lang('app.email')</strong> <br>
                        <p class="text-muted">{{ $client->email }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6"> <strong>@lang('modules.client.companyName')</strong> <br>
                        <p class="text-muted">{{ (count($client->client) > 0) ? ucwords($client->client[0]->company_name) : '-'}}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <nav>
                            <ul>
                                <li><a href="{{ route('member.clients.projects', $client->id) }}"><span>@lang('app.menu.projects')</span></a>
                                <li class="tab-current"><a href="{{ route('member.clients.invoices', $client->id) }}"><span>@lang('app.menu.invoices')</span></a>
                                </li>
                                <li><a href="{{ route('member.contacts.show', $client->id) }}"><span>@lang('app.menu.contacts')</span></a>
                                </li>
                                <li><a href="{{ route('member.notes.index') }}" class="waves-effect"><i class="fa fa-sticky-note-o"></i> <span class="hide-menu">@lang('modules.projects.notes') </span></a> </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-1" class="show">
                            <div class="row">


                                <div class="col-xs-12" >
                                    <div class="white-box">
                                        <h2>@lang('app.menu.invoices')</h2>

                                        <ul class="list-group" id="invoices-list">
                                            @forelse($invoices as $invoice)
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            @lang('app.invoice') # {{ $invoice->invoice_number }}
                                                        </div>
                                                        <div class="col-md-2">
                                                           {{ currency_formatter($invoice->total,$invoice->currency_symbol) }}
                                                        </div>
                                                        <div class="col-md-3">
                                                                <a href="{{ route('member.all-invoices.download', $invoice->id) }}" data-toggle="tooltip" data-original-title="Download" class="btn btn-default btn-circle"><i class="fa fa-download"></i></a>
                                                            <span class="m-l-10">{{ $invoice->issue_date->format($global->date_format) }}</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            No invoice for this client.
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
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

@endsection
