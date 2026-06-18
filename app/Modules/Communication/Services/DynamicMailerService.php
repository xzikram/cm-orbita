<?php

namespace App\Modules\Communication\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Illuminate\Mail\Mailer;

class DynamicMailerService
{
    /**
     * Configure Laravel's Mailer dynamically using the provided EmailAccount.
     */
    public function setMailer(EmailAccount $account): void
    {
        // Define a custom mailer configuration array
        $config = [
            'transport' => 'smtp',
            'host' => $account->smtp_host,
            'port' => $account->smtp_port,
            'encryption' => $account->encryption,
            'username' => $account->smtp_username,
            'password' => $account->smtp_password,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ];

        // Override the config for a specific custom mailer name
        Config::set('mail.mailers.dynamic', $config);
        
        // Also set the global from address to match the account
        Config::set('mail.from.address', $account->email_address);
        Config::set('mail.from.name', $account->name);

        // Purge the resolved mailer instance so it gets rebuilt with new config
        Mail::purge('dynamic');
    }

    /**
     * Get the dynamically configured Mailer instance.
     */
    public function getMailer(): \Illuminate\Contracts\Mail\Mailer
    {
        return Mail::mailer('dynamic');
    }
}
