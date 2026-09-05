<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ExpiryMail;
use App\Models\Products;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\SMSController;

class Expired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expired';
    protected $description = 'Your Product plan is expired.';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $clients = Products::with('entry')
            ->whereDate('expiry_date', '<', $today)
            ->where('expiry_mail_sent', false)
            ->get();

        // dd($clients);
        foreach ($clients as $client) {
            $smsTemplateId = env('EXPIRY_TEMPLATE_ID');
            $message = "Hi " . $client->entry->contact . ", We noticed that your services with Help Together Group has expired. We’d love to have you back! Please renew soon to continue enjoying our services. Contact us: +91 96346 44622";
            SMSController::sendSms($smsTemplateId, $message, $client->entry->contactno);
            Mail::to($client->entry->email)->send(new ExpiryMail($client));

            // Send email to the admin
            $adminEmail = env('ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new ExpiryMail($client));

            $client->update([
                'expiry_mail_sent' => true
            ]);
        }

        $this->info('Expired sent successfully.');
    }
}
