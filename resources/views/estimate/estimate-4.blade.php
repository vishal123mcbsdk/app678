<!DOCTYPE html>
<!--
  Invoice template by invoicebus.com
  To customize this template consider following this guide https://invoicebus.com/how-to-create-invoice-template/
  This template is under Invoicebus Template License, see https://invoicebus.com/templates/license/
-->
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@lang('app.invoice')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Invoice">

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

        @if($invoiceSetting->is_chinese_lang)
        @font-face {
            font-family: SimHei;
            /*font-style: normal;*/
            font-weight: bold;
            src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
        }
        @endif

        @php
            $font = '';
            if($invoiceSetting->locale == 'ja') {
                $font = 'ipag';
            } else if($invoiceSetting->locale == 'hi') {
                $font = 'hindi';
            } else if($invoiceSetting->locale == 'th') {
                $font = 'THSarabun';
            } else if($invoiceSetting->is_chinese_lang) {
                $font = 'SimHei';
            }else {
                $font = 'noto-sans';
            }
        @endphp
        @if($invoiceSetting->is_chinese_lang)
            body
        {
            font-weight: normal !important;
        }
        @endif

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

        .x-hidden {
            display: none !important;
        }

        .hidden {
            display: none;
        }

        b, strong, .bold {
            font-weight: bold;
        }

        #container {
            font: normal 13px/1.4em 'Open Sans', Sans-serif;
            margin: 0 auto;
            min-height: 1158px;
            position: relative;
        }

        .left-stripes {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100px;
            background: url("{{ asset("img/stripe-bg.jpg") }}") repeat;
        }
        .left-stripes .circle {
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            background: #415472;
            width: 30px;
            height: 30px;
            position: absolute;
            left: 33%;
        }
        .left-stripes .circle.c-upper {
            top: 440px;
        }
        .left-stripes .circle.c-lower {
            top: 690px;
        }

        .right-invoice {
            padding: 40px 30px 40px 130px;
            min-height: 1078px;
        }

        #memo .company-info {
            float: left;
        }
        #memo .company-info div {
            font-size: 28px;
            text-transform: uppercase;
            min-width: 20px;
            line-height: 1em;
        }
        #memo .company-info span {
            font-size: 12px;
            color: #858585;
            display: inline-block;
            min-width: 20px;
        }
        #memo .logo {
            float: right;
            margin-left: 15px;
        }
        #memo .logo img {
            max-height: 70px;
        }
        #memo:after {
            content: '';
            display: block;
            clear: both;
        }

        #invoice-title-number {
            margin: 50px 0 20px 0;
            display: inline-block;
            float: left;
        }
        #invoice-title-number .title-top {
            font-size: 15px;
            margin-bottom: 5px;
        }
        #invoice-title-number .title-top span {
            display: inline-block;
            min-width: 20px;
        }
        #invoice-title-number .title-top #number {
            text-align: right;
            float: right;
            color: #858585;
        }
        #invoice-title-number .title-top:after {
            content: '';
            display: block;
            clear: both;
        }
        #invoice-title-number #title {
            display: inline-block;
            background: #415472;
            color: white;
            font-size: 50px !important;
            padding: 20px 7px 5px 7px;
            line-height: 1em;
        }

        #client-info {
            /*float: right;*/
            text-align: right;
            /*margin-top: 50px;*/
            min-width: 220px;
        }
        .client-name {
            font-weight: bold !important;
            font-size: 15px !important;
            text-transform: uppercase;
            margin: 7px 0;
        }
        #client-info > div {
            margin-bottom: 3px;
            min-width: 20px;
        }
        #client-info span {
            display: block;
            min-width: 20px;
        }
        #client-info > span {
            text-transform: uppercase;
            color: #858585;
            font-size: 15px;
        }

        table {
            table-layout: fixed;
        }
        table th, table td {
            vertical-align: top;
            word-break: keep-all;
            word-wrap: break-word;
        }

        #invoice-info {
            float: left;
            margin-top: 10px;
        }
        #invoice-info div {
            margin-bottom: 3px;
        }
        #invoice-info div span {
            display: inline-block;
            min-width: 20px;
            min-height: 18px;
        }
        #invoice-info div span:first-child {
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 10px;
        }
        #invoice-info:after {
            content: '';
            display: block;
            clear: both;
        }

        .currency {
            margin-top: 20px;
            text-align: right;
            color: #858585;
            font-style: italic;
            font-size: 12px;
        }
        .currency span {
            display: inline-block;
            min-width: 20px;
        }

        #items {
            margin-top: 10px;
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
            font-size: 12px;
            text-transform: uppercase;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }
        #items table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }
        #items table th:last-child {
            /*text-align: right;*/
        }
        #items table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        #items table td:first-child {
            text-align: left;
        }
        #items table td:nth-child(2) {
            text-align: left;
        }

        #itemsPayment {
            margin-top: 10px;
        }
        #itemsPayment .first-cell, #itemsPayment table th:first-child, #itemsPayment table td:first-child {
            width: 18px;
            text-align: right;
        }
        #itemsPayment table {
            border-collapse: separate;
            width: 100%;
        }
        #itemsPayment table th {
            font-size: 12px;
            text-transform: uppercase;
            padding: 5px 3px;
            text-align: center;
            background: #b0b4b3;
            color: white;
        }
        #itemsPayment table th:nth-child(2) {
            width: 30%;
            text-align: left;
        }
        #itemsPayment table th:last-child {
            /*text-align: right;*/
        }
        #itemsPayment table td {
            padding: 10px 3px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        #itemsPayment table td:first-child {
            text-align: left;
        }
        #itemsPayment table td:nth-child(2) {
            text-align: left;
        }

        #sums {
            margin: 25px 30px 0 0;
            width: 100%;
        }
        #sums table {
            width: 70%;
            float: right;
        }
        #sums table tr th, #sums table tr td {
            min-width: 100px;
            padding: 9px 8px;
            text-align: right;
        }
        #sums table tr th {
            width: 70%;
            font-weight: bold;
            padding-right: 35px;
        }
        #sums table tr td.last {
            min-width: 0 !important;
            max-width: 0 !important;
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        #sums table tr.amount-total td {
            background: #415472 !important;
            color: white;
            font-size: 35px !important;
            line-height: 1em;
            padding: 7px !important;
        }
        #sums table tr.due-amount th, #sums table tr.due-amount td {
            font-weight: bold;
        }

        #sums:after {
            content: '';
            display: block;
            clear: both;
        }

        #terms {
            float: left;
            margin-top: 60px !important;
        }
        #terms > span {
            font-weight: bold;
            display: inline-block;
            min-width: 20px;
            text-transform: uppercase;
        }
        #terms > div {
            min-height: 50px;
            min-width: 50px;
        }

        #terms .notes {
            min-height: 30px;
            min-width: 50px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        /**
         * If the printed invoice is not looking as expected you may tune up
         * the print styles (you can use !important to override styles)
         */
        @media print {
            /* Here goes your print styles */
        }
        .page_break { page-break-before: always; }
        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }
        table td.text-center
        {
            text-align: center;
        }
        .descItem {
            border-bottom: none !important;
        }
        .item-summary {
            font-size: 10px;

        }
        table {
            page-break-inside: auto !important;
        }

        table tr {
            page-break-inside: auto !important;
        }

        table tr td {
            page-break-inside: auto !important;
        }
    </style>

</head>
<body>
<div id="container">
    <div class="left-stripes">
        <div class="circle c-upper"></div>
        <div class="circle c-lower"></div>
    </div>

    <div class="right-invoice">
        <section id="memo">
            <div class="company-info">
                <div>
                    {{ ucwords($global->company_name) }}
                </div>
                <br>
                <span>{!! nl2br($global->address) !!}</span>
                <br>
                <span>{{ $global->company_phone}}</span>

            </div>

            <div class="logo">
                <img src="{{ invoice_setting()->logo_url }}" alt="home" class="dark-logo" />
            </div>
        </section>

        <section id="invoice-title-number">
            <div id="title">{{ (is_null($estimate->estimate_number)) ? '#'.$estimate->id : $estimate->estimate_number }}</div>

        </section>
       
        <div class="clearfix"></div>
        <section id="invoice-info">
            
            <div>
                <span>@lang('app.status'):</span> <span>{{ __('app.'.$estimate->status) }}</span>
            </div>
            <div>@lang("modules.estimates.validTill"):
                         {{ $estimate->valid_till->format($global->date_format) }}
                        </div>

        </section>

        <div class="clearfix"></div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

        <section id="items">

            <table cellpadding="0" cellspacing="0">

                <tr>
                    <th>#</th> <!-- Dummy cell for the row number and row commands -->
                    <th>@lang("modules.invoices.item")</th>
                    @if($invoiceSetting->hsn_sac_code_show)
                         <th> @lang('modules.invoices.hsnSacCode')</th>
                     @endif
                    <th>@lang("modules.invoices.qty")</th>
                    <th>@lang("modules.invoices.unitPrice")  ({!! htmlentities($estimate->currency->currency_code)  !!})</th>
                    <th>@lang("modules.invoices.price") ({!! htmlentities($estimate->currency->currency_code)  !!})</th>
                </tr>

                <?php $count = 0; ?>
                @foreach($estimate->items as $item)
                    @if($item->type == 'item')
                        <tr data-iterate="item">
                            <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ ++$count }}</td> <!-- Don't remove this column as it's needed for the row commands -->
                            <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ ucfirst($item->item_name) }}</td>
                            @if($invoiceSetting->hsn_sac_code_show)
                                <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ $item->hsn_sac_code ?? '--' }}</td>
                            @endif
                            <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ $item->quantity }}</td>
                            <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ currency_formatter($item->unit_price,'')}}</td>
                            <td class="@if(!is_null($item->item_summary)) descItem @endif">{{ currency_formatter($item->amount,'') }}</td>
                        </tr>
                        @if(!is_null($item->item_summary))
                            <tr data-iterate="item">
                                <td ></td>
                                <td class="item-summary" @if($invoiceSetting->hsn_sac_code_show) colspan="4" @else colspan="3" @endif>
                                    {{ $item->item_summary }}
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endif
                @endforeach

            </table>

        </section>

        <section id="sums">

            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th>@lang("modules.invoices.subTotal"):</th>
                    <td>{{currency_formatter($estimate->sub_total,'')}}<</td>
                </tr>
                @if($discount != 0 && $discount != '')
                <tr data-iterate="tax">
                    <th>@lang("modules.invoices.discount"):</th>
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
                    <!-- {amount_total_label} -->
                    <td colspan="2">{{ currency_formatter($estimate->total,'') }}</td>
                </tr>
                
               
            </table>

        </section>

        <div class="clearfix"></div>
        <p>&nbsp;</p>

        <section id="terms">

            <div class="notes">
                @if(!is_null($estimate->note))
                    <br> {!! nl2br($estimate->note) !!}
                @endif
                    <br>{!! nl2br($invoiceSetting->estimate_terms) !!}
            </div>

        </section>

        @if($estimate->sign)
            <div style="text-align: right;">
                <h2 class="name" style="margin-bottom: 20px;">@lang('modules.estimates.signature') (@lang('app.customers'))</h2>
                <img src="{{ $estimate->sign->signature }}" style="width: 250px;">
            </div>
        @endif

        {{--Custom fields data--}}
        @if(isset($fields) && count($fields) > 0)
            <div class="page_break"></div>
            <div id="container">
                <div class="left-stripes">
                    <div class="circle c-upper"></div>
                    <div class="circle c-lower"></div>
                </div>

                <div class="invoice-body right-invoice b-all m-b-20">
                    <h3 class="box-title m-t-20 text-center h3-border"> @lang('modules.projects.otherInfo')</h3>
                    <table  style="background: none" border="0" cellspacing="0" cellpadding="0" width="100%">
                        @foreach($fields as $field)
                            <tr>
                                <td style="text-align: left;background: none;" >
                                    <div class="desc">{{ ucfirst($field->label) }}</div>
                                    <p id="notes">
                                        @if( $field->type == 'text')
                                            {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}
                                        @elseif($field->type == 'password')
                                            {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}
                                        @elseif($field->type == 'number')
                                            {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}

                                        @elseif($field->type == 'textarea')
                                            {{$invoice->custom_fields_data['field_'.$field->id] ?? '-'}}

                                        @elseif($field->type == 'radio')
                                            {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? $invoice->custom_fields_data['field_'.$field->id] : '-' }}
                                        @elseif($field->type == 'select')
                                            {{ (!is_null($invoice->custom_fields_data['field_'.$field->id]) && $invoice->custom_fields_data['field_'.$field->id] != '') ? $field->values[$invoice->custom_fields_data['field_'.$field->id]] : '-' }}
                                        @elseif($field->type == 'checkbox')
                                            {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? $field->values[$invoice->custom_fields_data['field_'.$field->id]] : '-' }}
                                        @elseif($field->type == 'date')
                                            {{ !is_null($invoice->custom_fields_data['field_'.$field->id]) ? \Carbon\Carbon::parse($invoice->custom_fields_data['field_'.$field->id])->format($global->date_format) : '--'}}
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

            </div>

        @endif

    </div>
</div>

</body>
</html>
