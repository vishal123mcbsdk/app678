<?php

namespace App\Listeners;

use App\Events\NewExpenseEvent;
use App\Events\NewExpenseRecurringEvent;
use App\Events\NewInvoiceRecurringEvent;
use App\Notifications\ExpenseRecurringStatus;
use App\Notifications\InvoiceRecurringStatus;
use App\Notifications\NewExpenseAdmin;
use App\Notifications\NewExpenseMember;
use App\Notifications\NewExpenseRecurringMember;
use App\Notifications\NewExpenseStatus;
use App\Notifications\NewRecurringInvoice;
use App\Scopes\CompanyScope;
use App\User;
use Illuminate\Support\Facades\Notification;

class NewInvoiceRecurringListener
{

    /**
     * NewExpenseRecurringListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param NewInvoiceRecurringEvent $event
     */
    public function handle(NewInvoiceRecurringEvent $event)
    {
        if (request()->type && request()->type == 'send') {
            if (($event->invoice->project && $event->invoice->project->client_id != null) || $event->invoice->client_id != null) {
                $clientId = ($event->invoice->project && $event->invoice->project->client_id != null) ? $event->invoice->project->client_id : $event->invoice->client_id;
                // Notify client
                $notifyUser = User::withoutGlobalScopes([CompanyScope::class, 'active'])->findOrFail($clientId);

                if ($event->status == 'status') {
                    Notification::send($notifyUser, new InvoiceRecurringStatus($event->invoice));
                }
                else{
                    Notification::send($notifyUser, new NewRecurringInvoice($event->invoice));
                }
            }
        }

    }

}
