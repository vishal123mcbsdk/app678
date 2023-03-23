<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentsModuleClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = \App\Company::all();

        foreach($companies as $company) {
            $modulesClient = new \App\ModuleSetting();
            $modulesClient->module_name = 'payments';
            $modulesClient->company_id = $company->id;
            $modulesClient->type = 'client';
            $modulesClient->status = 'active';
            $modulesClient->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\ModuleSetting::where('module_name', 'payments')->where('type', 'client')->delete();
    }
}
