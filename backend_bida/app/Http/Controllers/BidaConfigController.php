<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BidaConfigController extends Controller
{
    public function getHourlyRates()
    {
        return response()->json([
            'lo' => (int) config('bida.hourly_rates.lo', 50000),
            'phang' => (int) config('bida.hourly_rates.phang', 50000),
            'lo_vip' => (int) config('bida.hourly_rates.lo_vip', 80000),
            'phang_vip' => (int) config('bida.hourly_rates.phang_vip', 80000),
        ]);
    }

    public function setHourlyRates(Request $request)
    {
        $payload = $request->validate([
            'lo' => 'required|integer|min:0',
            'phang' => 'required|integer|min:0',
            'lo_vip' => 'required|integer|min:0',
            'phang_vip' => 'required|integer|min:0',
        ]);
        $envPath = base_path('.env');
        $env = file_exists($envPath) ? file_get_contents($envPath) : '';

        // Helper: replace hoặc append biến env
        $setEnvVar = function ($key, $value) use (&$env) {
            if (preg_match('/^' . $key . '=.*/m', $env)) {
                $env = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $env);
            } else {
                $env = rtrim($env) . "\n" . $key . '=' . $value . "\n";
            }
        };

        $setEnvVar('BIDA_HOURLY_RATE_LO', $payload['lo']);
        $setEnvVar('BIDA_HOURLY_RATE_PHANG', $payload['phang']);
        $setEnvVar('BIDA_HOURLY_RATE_LO_VIP', $payload['lo_vip']);
        $setEnvVar('BIDA_HOURLY_RATE_PHANG_VIP', $payload['phang_vip']);

        file_put_contents($envPath, $env);
        // Reload config
        Artisan::call('config:clear');
        return response()->json([
            'message' => 'Cập nhật giá giờ thành công',
            'lo' => $payload['lo'],
            'phang' => $payload['phang'],
            'lo_vip' => $payload['lo_vip'],
            'phang_vip' => $payload['phang_vip'],
        ]);
    }
}
