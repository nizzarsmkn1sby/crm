<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Company
            ['key' => 'company_name',      'value' => 'WebCare CRM',     'type' => 'string',  'group' => 'company'],
            ['key' => 'company_email',     'value' => '',                'type' => 'string',  'group' => 'company'],
            ['key' => 'company_phone',     'value' => '',                'type' => 'string',  'group' => 'company'],
            ['key' => 'company_address',   'value' => '',                'type' => 'string',  'group' => 'company'],
            // WhatsApp
            ['key' => 'whatsapp_provider', 'value' => 'fonnte',          'type' => 'string',  'group' => 'whatsapp'],
            ['key' => 'whatsapp_token',    'value' => env('WHATSAPP_TOKEN', ''), 'type' => 'string', 'group' => 'whatsapp'],
            ['key' => 'whatsapp_sender',   'value' => env('WHATSAPP_SENDER', ''), 'type' => 'string', 'group' => 'whatsapp'],
            // Email
            ['key' => 'mail_host',         'value' => env('MAIL_HOST', 'smtp.gmail.com'), 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_port',         'value' => env('MAIL_PORT', '587'),            'type' => 'integer', 'group' => 'email'],
            ['key' => 'mail_username',     'value' => env('MAIL_USERNAME', ''),           'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => env('MAIL_FROM_ADDRESS', ''),       'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_name',    'value' => env('MAIL_FROM_NAME', 'WebCare CRM'), 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_encryption',   'value' => env('MAIL_ENCRYPTION', 'tls'),      'type' => 'string', 'group' => 'email'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
