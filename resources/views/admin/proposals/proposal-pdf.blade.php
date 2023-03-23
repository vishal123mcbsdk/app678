<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>@lang('app.menu.proposal') # {{ $proposal->id }}</title>
    <style>
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ asset('fonts/TH_Sarabun.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunBold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunBoldItalic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: bold;
            src: url("{{ asset('fonts/TH_SarabunItalic.ttf') }}") format('truetype');
        }
        @if( $global->locale == 'zh-hk' ||  $global->locale == 'zh-cn' ||  $global->locale == 'zh-sg' ||
                        $global->locale == 'zh-tw' ||  $global->locale == 'cn')
@font-face {
            font-family: SimHei;
            /*font-style: normal;*/
            font-weight: bold;
            src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
        }
        @endif

        @php
            $font = '';
            if( $global->locale == 'ja') {
                $font = 'ipag';
            } else if( $global->locale == 'hi') {
                $font = 'hindi';
            } else if( $global->locale == 'th') {
                $font = 'THSarabun';
            } else if( $global->locale == 'zh-hk' ||  $global->locale == 'zh-cn' ||  $global->locale == 'zh-sg' ||
             $global->locale == 'zh-tw' ||  $global->locale == 'cn') {
                $font = 'SimHei';
            }else {
                $font = 'noto-sans';
            }
        @endphp
        @if( $global->locale == 'zh-hk' ||  $global->locale == 'zh-cn' ||  $global->locale == 'zh-sg' ||
             $global->locale == 'zh-tw' ||  $global->locale == 'cn')
            body
        {
            font-weight: normal !important;
        }
        @endif
         * {
            font-family: {{$font}}, DejaVu Sans , sans-serif;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 100%;
            height: auto;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: 'DejaVu Sans', sans-serif;
        }

        h2 {
            font-weight:normal;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 11px;
        }

        #logo img {
            height: 55px;
            margin-bottom: 15px;
        }

        #company {

        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {

        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 5px 10px 7px 10px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #767676;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #767676;
            width: 10%;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
        }


        table .total {
            background: #767676;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total
        {
            font-size: 1.2em;
            text-align: center;
        }

        table td.unit{
            width: 35%;
        }

        table td.desc{
            width: 45%;
        }

        table td.qty{
            width: 5%;
        }

        .status {
            margin-top: 15px;
            padding: 1px 8px 5px;
            font-size: 1.3em;
            width: 80px;
            color: #fff;
            float: right;
            text-align: center;
            display: inline-block;
        }

        .status.unpaid {
            background-color: #E7505A;
        }
        .status.paid {
            background-color: #26C281;
        }
        .status.cancelled {
            background-color: #95A5A6;
        }
        .status.error {
            background-color: #F4D03F;
        }

        table tr.tax .desc {
            text-align: right;
            color: #1BA39C;
        }
        table tr.discount .desc {
            text-align: right;
            color: #E43A45;
        }
        table tr.subtotal .desc {
            text-align: right;
            color: #1d0707;
        }
        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 10px 20px 10px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-bottom: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        table.billing td {
            background-color: #fff;
        }

        table td div#invoiced_to {
            text-align: left;
        }

        #notes{
            color: #767676;
            font-size: 11px;
        }

    </style>
</head>
<body>
<header class="clearfix">

    <table cellpadding="0" cellspacing="0" class="billing">
        <tr>
            <td>
                <div id="invoiced_to">
                    @if(!is_null($proposal->lead_id))
                        <small>@lang("app.to"):</small>
                        <h2 class="name">{{ $proposal->lead->company_name or ucwords($proposal->lead->client_name) }}</h2>
                        <div>{!! nl2br($proposal->lead->address) !!}</div>
                    @endif
                </div>
            </td>
            <td>
                <div id="company">
                    <small>@lang("modules.invoices.generatedBy"):</small>
                    <h2 class="name">{{ ucwords($global->company_name) }}</h2>
                    @if(!is_null($settings))
                        <div>{!! nl2br($global->address) !!}</div>
                        <div>P: {{ $global->company_phone }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</header>
<main>
    <div id="details" class="clearfix">

        <div id="invoice">
            <h1>@lang("modules.proposal.proposal") #{{ $proposal->id }} wadaDASDASDGASFZ</h1>
            <div class="date">@lang("modules.estimates.validTill"): {{ $proposal->valid_till->format($global->date_format) }} SDVsdvsv</div>
        </div>

    </div>
    @php
        $colspan = ($invoiceSetting->hsn_sac_code_show) ? 5 : 4;
    @endphp
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="no">#</th>
            <th class="desc">@lang("modules.invoices.item")</th>
            @if($invoiceSetting->hsn_sac_code_show)
                <th class="desc"> @lang('modules.invoices.hsnSacCode')</th>
            @endif
            <th class="qty">@lang("modules.invoices.qty")</th>
            <th class="qty">@lang("modules.invoices.unitPrice") ({!! htmlentities($proposal->currency->currency_code)  !!})</th>
            <th class="unit">@lang("modules.invoices.price") ({!! htmlentities($proposal->currency->currency_code)  !!})</th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 0; ?>
        @foreach($proposal->items as $item)
            @if($item->type == 'item')
            <tr style="page-break-inside: avoid;">
                <td class="no">{{ ++$count }}</td>
                <td class="desc"><h3>{{ ucfirst($item->item_name) ?? '--' }}</h3></td>
                @if($invoiceSetting->hsn_sac_code_show)
                    <td class="qty">{{ $item->hsn_sac_code ?? '--' }}</td>
                @endif
                <td class="qty"><h3>{{ $item->quantity }}</h3></td>
                <td class="qty"><h3>{{ $item->unit_price }}</h3></td>
                <td class="unit">{{ currency_formatter($item->amount,'') }}</td>
            </tr>
            @endif
        @endforeach
        <tr style="page-break-inside: avoid;" class="subtotal">
            <td class="no">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            @if($invoiceSetting->hsn_sac_code_show)
                <td class="qty"> </td>
            @endif
            <td class="qty">&nbsp;</td>
            <td class="desc">@lang("modules.invoices.subTotal")</td>
            <td class="unit">{{ currency_formatter($proposal->sub_total,'') }}</td>
        </tr>
        <tr style="page-break-inside: avoid;" class="discount">
            <td class="no">&nbsp;</td>
            <td class="qty">&nbsp;</td>
            @if($invoiceSetting->hsn_sac_code_show)
                <td class="qty"> </td>
            @endif
            <td class="qty">&nbsp;</td>
            <td class="desc">@lang("modules.invoices.discount")</td>
            <td class="unit">-{{ currency_formatter($discount,'') }}</td>
        </tr>
        @foreach($taxes as $key => $tax)
            <tr style="page-break-inside: avoid;" class="tax">
                <td class="no">&nbsp;</td>
                <td class="qty">&nbsp;</td>
                @if($invoiceSetting->hsn_sac_code_show)
                    <td class="qty"> </td>
                @endif
                <td class="qty">&nbsp;</td>
                <td class="desc">{{ strtoupper($key) }}</td>
                <td class="unit">{{ currency_formatter($tax,'') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr dontbreak="true">
            <td colspan="{{ $colspan }}">@lang("modules.invoices.total")</td>
            <td>{{ currency_formatter($proposal->total,'') }}</td>
        </tr>
        </tfoot>
    </table>
    <p>&nbsp;</p>


    @if($proposal->signature)
        <div style="text-align: right;">
            <h2 class="name" style="margin-bottom: 20px;">@lang('modules.estimates.signature')</h2>
            <img src="{{ $proposal->signature->signature }}" style="width:250px">

            <p>{{ ucwords($proposal->signature->full_name) }}</p>
        </div>
    @endif
    @if($proposal->client_comment)
        <div>
            <h5 class="name" style="margin-bottom: 20px;">@lang('app.comment')</h5>
            <p> {{ $proposal->client_comment }} </p>
        </div>
    @endif

    <hr>
    <p id="notes">

        {!! nl2br(ucfirst($proposal->note)) !!}
    </p>

</main>
</body>
</html>