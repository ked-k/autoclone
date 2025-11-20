<?php
namespace App\Services;

use App\Models\LoginRecord as LoginActivityModel;
use App\Models\UserManagement\UserActivity\ExportLog;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

class LoginActivityService
{
    public static function addToLog($description, $email, $ip): void
    {
        $log      = [];
        $platform = Agent::platform();
        $browser  = Agent::browser();

        $log['user_id']     = auth()->check() ? auth()->user()->id : null;
        $log['email']       = $email;
        $log['description'] = $description;
        $log['platform']    = $platform;
        $log['browser']     = $browser;
        $log['client_ip']   = $ip;

        LoginActivityModel::create($log);
    }

    public static function addExportToLog($type): void
    {
        ExportLog::create([
            'user_id'    => Auth::id(), // `null` if guest
            'type'       => $type,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
