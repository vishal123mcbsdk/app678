<!DOCTYPE html>
<!--
  creditNote template by invoicebus.com
  To customize this template consider following this guide https://invoicebus.com/how-to-create-invoice-template/
  This template is under Invoicebus Template License, see https://invoicebus.com/templates/license/
-->
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@lang('app.credit-note')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="creditNote">

    <style>
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/TH_Sarabun.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunBoldItalic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ storage_path('fonts/TH_SarabunItalic.ttf') }}") format('truetype');
        }

        @php
            $font = '';
            if($company->locale == 'ja') {
                $font = 'ipag';
            } else if($company->locale == 'hi') {
                $font = 'hindi';
            } else if($company->locale == 'th') {
                $font = 'THSarabun';
            } else {
                $font = 'noto-sans';
            }
        @endphp

        * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }

        html {
            line-height: 1;
        }

        ol, ul {
            list-style: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        caption, th, td {
            text-align: left;
            font-weight: normal;
            vertical-align: middle;
        }

        q, blockquote {
            quotes: none;
        }
        q:before, q:after, blockquote:before, blockquote:after {
            content: "";
            content: none;
        }

        a img {
            border: none;
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
            display: block;
        }

        /* Invoice styles */
        /**
         * DON'T override any styles for the <html> and <body> tags, as this may break the layout.
         * Instead wrap everything in one main <div id="container"> element where you may change
         * something like the font or the background of the invoice
         */
        html, body {
            /* MOVE ALONG, NOTHING TO CHANGE HERE! */
        }

        /**
         * IMPORTANT NOTICE: DON'T USE '!important' otherwise this may lead to broken print layout.
         * Some browsers may require '!important' in oder to work properly but be careful with it.
         */
        .clearfix {
            display: block;
            clear: both;
        }

        .hidden {
            display: none;
        }

        b, strong, .bold {
            font-weight: bold;
        }

        #container {
            font: normal 13px 'Open Sans', Sans-serif;
            margin: 0 auto;
            min-height: 1078px;
        }

        .creditNote-top {
            background: #242424;
            color: #fff;
            padding: 40px 40px 50px 40px;
        }

        .creditNote-body {
            padding: 50px 40px 40px 40px;
        }

        #memo .logo {
            float: left;
            margin-right: 20px;
        }
        #memo .logo img {
            max-width: 150px;
        }
        #memo .company-info {
            /*float: right;*/
            text-align: right;
        }
        #memo .company-info .company-name {
            color: #F8ED31;
            font-weight: bold;
            font-size: 32px;
        }
        #memo .company-info .spacer {
            height: 15px;
            display: block;
        }
        #memo .company-info div {
            font-size: 12px;
            float: right;
            margin: 0 18px 3px 0;
        }
        #memo:after {
            content: '';
            display: block;
            clear: both;
        }

        #creditNote-info {
            float: left;
            margin-top: 50px;
        }

        #creditNote-info table{
            width: 30%;
        }
        #creditNote-info > div {
            float: left;
        }
        #creditNote-info > div > span {
            display: block;
            min-width: 100px;
            min-height: 18px;
            margin-bottom: 3px;
        }
        #creditNote-info > div:last-of-type {
            margin-left: 10px;
            text-align: right;
        }
        #creditNote-info:after {
            content: '';
            display: block;
            clear: both;
        }

        #client-info {
            float: right;
            /*margin-top: 5px;*/
            margin-right: 30px;
            min-width: 220px;
        }
        #client-info > div {
            margin-bottom: 3px;
        }
        #client-info span {
            display: block;
        }
        #client-info > span {
            margin-bottom: 3px;
        }

        #creditNote-title-number {
            margin-top: 30px;
        }
        #creditNote-title-number #title {
            margin-right: 5px;
            text-align: right;
            font-size: 35px;
        }
        #creditNote-title-number #number {
            margin-left: 5px;
            text-align: left;
            font-size: 20px;
            padding-bottom: 10px;
        }

        table {
            table-layout: fixed;
        }
        table th, table td {
            vertical-align: top;
            word-break: keep-all;
            word-wrap: break-word;
        }

        #items .first-cell, #items table th:first-child, #items table td:first-child {
            width: 18px;
            text-align: right;
        }
        #items table {
            border-collapse: separate;
            width: 100%;
        }
        #items table th {
            font-weight: bold;
            padding: 12px 10px;
            text-align: right;
            border-bottom: 1px solid #444;
            text-transform: uppercase;
        }
        #items table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }
        #items table th:last-child {
            text-align: right;
        }
        #items table td {
            border-right: 1px solid #b6b6b6;
            padding: 15px 10px;
            text-align: right;
        }
        #items table td:first-child {
            text-align: left;
            border-right: none !important;
        }
        #items table td:nth-child(2) {
            text-align: left;
        }
        #items table td:last-child {
            border-right: none !important;
        }

        #sums {
            float: right;
            /*margin-right: 100px;*/
            margin-top: 30px;
        }

        #sums table{
            width: 50%;
        }
        #sums table tr th, #sums table tr td {
            min-width: 100px;
            padding: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }
        #sums table tr th {
            width: 70%;
            padding-right: 25px;
            color: #707070;
        }
        /*#sums table tr td:last-child {*/
            /*min-width: 0 !important;*/
            /*max-width: 0 !important;*/
            /*width: 0 !important;*/
            /*padding: 0 !important;*/
            /*overflow: visible;*/
        /*}*/
        #sums table tr.amount-total th {
            color: black;
        }
        #sums table tr.amount-total th, #sums table tr.amount-total td {
            font-weight: bold;
        }
        /*#sums table tr.amount-total td:last-child {*/
            /*position: relative;*/
        /*}*/
        /*#sums table tr.amount-total td:last-child .currency {*/
            /*position: absolute;*/
            /*top: 3px;*/
            /*left: -740px;*/
            /*font-weight: normal;*/
            /*font-style: italic;*/
            /*font-size: 12px;*/
            /*color: #707070;*/
        /*}*/
        /*#sums table tr.amount-total td:last-child:before {*/
            /*display: block;*/
            /*content: '';*/
            /*border-top: 1px solid #444;*/
            /*position: absolute;*/
            /*top: 0;*/
            /*left: -740px;*/
            /*right: 0;*/
        /*}*/
        /*#sums table tr:last-child th, #sums table tr:last-child td {*/
            /*color: black;*/
        /*}*/

        #terms {
            margin: 100px 0 15px 0;
        }
        #terms > div {
            min-height: 70px;
        }

        .payment-info {
            color: #707070;
            font-size: 12px;
        }
        .payment-info div {
            display: inline-block;
            min-width: 10px;
        }

        .ib_drop_zone {
            color: #F8ED31 !important;
            border-color: #F8ED31 !important;
        }

        /**
         * If the printed invoice is not looking as expected you may tune up
         * the print styles (you can use !important to override styles)
         */
        @media print {
            /* Here goes your print styles */
        }

    </style>
</head>
<body>
<div id="container">
    <div class="creditNote-top">
        <section id="memo">
            <div class="logo">
                <img src="{{ $global->logo_url }}" alt="home" class="dark-logo"/>
            </div>

            <div class="company-info">
                <span class="company-name">
                <?php
                    $company = explode(' ', trim($global->company_name));
                    echo $company[0];
                    ?>
                </span>

                <span class="spacer"></span>

                <div>{!! nl2br($global->address) !!}</div>


                <span class="clearfix"></span>

                <div>{{ $global->company_phone }}</div>

                <span class="clearfix"></span>

                @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNoteSetting->gst_number))
                    <div>@lang('app.gstIn'): {{ $creditNoteSetting->gst_number }}</div>
                @endif
            </div>

        </section>

        <section id="creditNote-info">
            <table>
                <tr>
                    <td>@lang('app.menu.issues') @lang('app.date'):</td>
                    <td>{{ $creditNote->issue_date->format($global->date_format) }}</td>
                </tr>
                @if($creditNote->status == 'open')
                    <tr>
                        <td>@lang('app.dueDate'):</td>
                        <td>{{ $creditNote->due_date->format($global->date_format) }}</td>
                    </tr>
                    @endif
                @if($invoiceNumber)
                    <tr>
                        <td>@lang('app.invoiceNumber'):</td>
                        <td>{{ $invoiceNumber->invoice_number }}</td>
                    </tr>
                @endif
                <tr>
                    <td>@lang('app.status'):</td>
                    <td>{{ __('app.'.$creditNote->status) }}</td>
                </tr>
            </table>


            <div class="clearfix"></div>

            <section id="creditNote-title-number">

                <span id="title">{{ $creditNoteSetting->credit_note_prefix }}</span>
                <span id="number">{{ $creditNote->original_cn_number }}</span>

            </section>
        </section>
        @if(!is_null($creditNote->project) && !is_null($creditNote->project->client))
            <section id="client-info">
                <span>@lang('app.billedTo'):</span>
                <div class="client-name">
                    <strong>{{ ucwords($creditNote->project->client->name) }}</strong>
                </div>

                <div>
                    <span>{{ ucwords($creditNote->project->client->company_name) }}</span>
                </div>

                <div>
                    <span>{!! nl2br($creditNote->project->client->address) !!}</span>
                </div>

                <div>
                    <span>{{ $creditNote->project->client->email }}</span>
                </div>
                @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->project->client->gst_number))
                    <div>
                        <span> @lang('app.gstIn'): {{ $creditNote->project->client->gst_number }} </span>
                    </div>
                @endif
            </section>

        @elseif(!is_null($creditNote->client))
            <section id="client-info">
                <span>@lang('app.billedTo'):</span>
                <div class="client-name">
                    <strong>{{ ucwords($creditNote->client->name) }}</strong>
                </div>

                <div>
                    <span>{{ ucwords($creditNote->client->company_name) }}</span>
                </div>

                <div>
                    <span>{!! nl2br($creditNote->client->address) !!}</span>
                </div>

                <div>
                    <span>{{ $creditNote->client->email }}</span>
                </div>
                @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->client->gst_number))
                    <div>
                        <span> @lang('app.gstIn'): {{ $creditNote->client->gst_number }} </span>
                    </div>
                @endif
            </section>
        @elseif(is_null($creditNote->client_id) && is_null($creditNote->project_id))

            @if($creditNote->invoice->client)
                <section id="client-info">
                    <span>@lang('app.billedTo'):</span>
                    <div class="client-name">
                        <strong>{{ ucwords($creditNote->invoice->client->name) }}</strong>
                    </div>

                    <div>
                        <span>{{ ucwords($creditNote->invoice->client->company_name) }}</span>
                    </div>

                    <div>
                        <span>{!! nl2br($creditNote->invoice->client->address) !!}</span>
                    </div>

                    <div>
                        <span>{{ $creditNote->invoice->client->email }}</span>
                    </div>
                    @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->invoice->client->gst_number))
                        <div>
                            <span> @lang('app.gstIn'): {{ $creditNote->invoice->client->gst_number }} </span>
                        </div>
                    @endif
                </section>
            @elseif($creditNote->invoice->project)
                <section id="client-info">
                    <span>@lang('app.billedTo'):</span>
                    <div class="client-name">
                        <strong>{{ ucwords($creditNote->invoice->project->client->name) }}</strong>
                    </div>

                    <div>
                        <span>{{ ucwords($creditNote->invoice->project->client->company_name) }}</span>
                    </div>

                    <div>
                        <span>{!! nl2br($creditNote->invoice->project->client->address) !!}</span>
                    </div>

                    <div>
                        <span>{{ $creditNote->invoice->project->client->email }}</span>
                    </div>
                    @if($creditNoteSetting->show_gst == 'yes' && !is_null($creditNote->invoice->project->client->gst_number))
                        <div>
                            <span> @lang('app.gstIn'): {{ $creditNote->invoice->project->client->gst_number }} </span>
                        </div>
                    @endif
                </section>
            @endif
        @endif
        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div class="creditNote-body">
        <section id="items">

            <table cellpadding="0" cellspacing="0">

                <tr>
                    <th>#</th> <!-- Dummy cell for the row number and row commands -->
                    <th>@lang("modules.credit-notes.item")</th>
                    @if($invoiceSetting->hsn_sac_code_show)
                        <th >@lang('modules.invoices.hsnSacCode')</th>
                    @endif
                    <th>@lang("modules.credit-notes.qty")</th>
                    <th>@lang("modules.credit-notes.unitPrice") ({!! htmlentities($creditNote->currency->currency_code)  !!})</th>
                    <th>@lang("modules.credit-notes.price") ({!! htmlentities($creditNote->currency->currency_code)  !!})</th>
                </tr>

                <?php $count = 0; ?>
                @foreach($creditNote->items as $item)
                    @if($item->type == 'item')
                <tr data-iterate="item">
                    <td>{{ ++$count }}</td> <!-- Don't remove this column as it's needed for the row commands -->
                    <td>{{ ucfirst($item->item_name) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ currency_formatter($item->unit_price,'') }}</td>
                    <td>{{ currency_formatter($item->amount,'') }}</td>
                </tr>
                    @endif
                @endforeach

            </table>

        </section>

        <section id="sums">

            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th>@lang("modules.credit-notes.subTotal"):</th>
                    <td>{{ currency_formatter($creditNote->sub_total, '') }}</td>
                </tr>
                @if($discount != 0 && $discount != '')
                <tr data-iterate="tax">
                    <th>@lang("modules.credit-notes.discount"):</th>
                    <td>-{{ currency_formatter($discount, '') }}</td>
                </tr>
                @endif
                @foreach($taxes as $key=>$tax)
                <tr data-iterate="tax">
                    <th>{{ strtoupper($key) }}:</th>
                    <td>{{ currency_formatter($tax, '') }}</td>
                </tr>
                @endforeach

                <tr class="amount-total">
                    <th>
                        <hr>@lang("modules.credit-notes.total"):</th>
                    <td>
                        <hr>{{ currency_formatter($creditNote->total, '') }}</td>
                </tr>
                <tr>
                    <th>
                        <hr>
                        @lang("modules.credit-notes.creditAmountUsed"):
                    </th>
                    <td>
                        <hr>
                        {{ currency_formatter($creditNote->creditAmountUsed(),'') }}
                    </td>
                </tr>
                <tr>
                    <th>
                        @lang("modules.credit-notes.creditAmountRemaining"):
                    </th>
                    <td>
                        {{ currency_formatter($creditNote->creditAmountRemaining(),'') }}
                    </td>
                </tr>
            </table>

        </section>

        <div class="clearfix"></div>
        <hr>
        <section id="terms">
            <span class="hidden">Terms:</span>
           
            @if(!is_null($creditNote->note))
                <div>{!! nl2br($creditNote->note) !!}</div>
            @endif
            @if($creditNote->status == 'open')
            <div>{!! nl2br($creditNoteSetting->credit_note_terms) !!}</div>
            @endif

        </section>


    </div>

</div>

</body>
</html>
