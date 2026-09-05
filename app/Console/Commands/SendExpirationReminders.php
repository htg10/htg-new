<?php

namespace App\Console\Commands;

use App\Models\Entry;
use Illuminate\Console\Command;
use App\Mail\ExpirationReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Products;
use App\Http\Controllers\Admin\SMSController;
use Carbon\Carbon;

class SendExpirationReminders extends Command
{
    protected $signature = 'send:expiry';
    protected $description = 'Your Product plan is expired after 15 days';

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        // $tomorrow = now()->addDay(0)->toDateString();
        $fifteenDaysFromNow = now()->addDays(15)->toDateString();
        $clients = Products::with('entry')
            ->whereDate('expiry_date', $fifteenDaysFromNow)
            ->get();

        // dd($clients);
        foreach ($clients as $client) {
            $smsTemplateId = env('BEFORE_EXPIRY_EMPLATE_ID');
            $message = "Hi" . $client->entry->contact . ", We noticed that your services with Help Together Group has expired. We’d love to have you back! Please renew soon to continue enjoying our services. Contact us: +91 96346 44622";
            // $message = "Your Services with Help Together Group is up for renewal. Please renew by" . $client->entry->contact . " to avoid interruptions. For More Info Call us  +91 96346 44622.";
            SMSController::sendSms($smsTemplateId, $message, $client->entry->contactno);
            Mail::to($client->entry->email)->send(new ExpirationReminderMail($client));

            // Send email to the admin
            $adminEmail = env('ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new ExpirationReminderMail($client));
        }

        $this->info('Expiration reminders sent successfully.');
    }
}
