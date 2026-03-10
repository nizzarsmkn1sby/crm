<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name'      => 'nullable|string|max:255',
            'company_address'   => 'nullable|string',
            'company_phone'     => 'nullable|string|max:50',
            'company_email'     => 'nullable|email|max:255',
            'whatsapp_provider' => 'nullable|string|in:fonnte,ultramsg',
            'whatsapp_token'    => 'nullable|string',
            'whatsapp_sender'   => 'nullable|string|max:50',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,',
        ]);

        $groups = [
            'company_name'      => ['group' => 'company'],
            'company_address'   => ['group' => 'company'],
            'company_phone'     => ['group' => 'company'],
            'company_email'     => ['group' => 'company'],
            'whatsapp_provider' => ['group' => 'whatsapp'],
            'whatsapp_token'    => ['group' => 'whatsapp'],
            'whatsapp_sender'   => ['group' => 'whatsapp'],
            'mail_host'         => ['group' => 'email'],
            'mail_port'         => ['group' => 'email', 'type' => 'integer'],
            'mail_username'     => ['group' => 'email'],
            'mail_password'     => ['group' => 'email'],
            'mail_from_address' => ['group' => 'email'],
            'mail_from_name'    => ['group' => 'email'],
            'mail_encryption'   => ['group' => 'email'],
        ];

        foreach ($groups as $key => $meta) {
            if ($request->has($key)) {
                $value = $request->input($key);
                // Don't overwrite passwords with empty string
                if (in_array($key, ['whatsapp_token', 'mail_password']) && empty($value)) {
                    continue;
                }
                Setting::set($key, $value, $meta['type'] ?? 'string', $meta['group']);
            }
        }

        // Update .env file for mail config so Laravel uses it
        $this->updateEnvFile([
            'MAIL_HOST'       => $request->mail_host ?? '',
            'MAIL_PORT'       => $request->mail_port ?? '587',
            'MAIL_USERNAME'   => $request->mail_username ?? '',
            'MAIL_ENCRYPTION' => $request->mail_encryption ?? 'tls',
            'MAIL_FROM_ADDRESS' => $request->mail_from_address ?? '',
            'MAIL_FROM_NAME'  => $request->mail_from_name
                ? '"' . $request->mail_from_name . '"'
                : '"WebCare CRM"',
            'WHATSAPP_TOKEN'  => $request->whatsapp_token ?: env('WHATSAPP_TOKEN', ''),
            'WHATSAPP_SENDER' => $request->whatsapp_sender ?? '',
            'WHATSAPP_PROVIDER' => $request->whatsapp_provider ?? 'fonnte',
        ]);

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $escaped = preg_quote("={$value}", '/');
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
