<?php

namespace App\Console\Commands;

use App\Company;
use App\Currency;
use App\GlobalSetting;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the exchange rates for all the currencies in currencies table.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currencies = Currency::all();
        $setting = Company::first();

        $currencyApiKey = GlobalSetting::first()->currency_converter_key;
        $currencyApiKeyVersion = GlobalSetting::first()->currency_key_version;

        $currencyApiKey = ($currencyApiKey) ? $currencyApiKey : env('CURRENCY_CONVERTER_KEY');

        foreach ($currencies as $currency) {

            $currency = Currency::findOrFail($currency->id);

            // Get exchange rate
            $client = new Client();
            $res = $client->request('GET', 'https://' . $currencyApiKeyVersion . '.currconv.com/api/v7/convert?q=' . $setting->currency->currency_code . '_' . $currency->currency_code . '&compact=ultra&apiKey=' . $currencyApiKey);
            $conversionRate = $res->getBody();
            $conversionRate = json_decode($conversionRate, true);

            $currency->exchange_rate = $conversionRate[$setting->currency->currency_code . '_' . $currency->currency_code];
            $currency->save();
        }
    }

}
