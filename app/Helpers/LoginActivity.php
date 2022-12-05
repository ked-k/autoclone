<?php

namespace App\Helpers;

use App\Models\LoginRecord as LoginActivityModel;
use Jenssegers\Agent\Facades\Agent;
use Request;

class LoginActivity
{
    public static function addToLog($description, $email, $ip)
    {
        $log = [];
        $platform = Agent::platform();
        $browser = Agent::browser();

        $log['user_id'] = auth()->check() ? auth()->user()->id : null;
        $log['email'] = $email;
        $log['description'] = $description;
        $log['platform'] = $platform;
        $log['browser'] = $browser;
        // $log['url'] = Request::fullUrl();
        // $log['method'] = Request::method();
        $log['client_ip'] = $ip;
        LoginActivityModel::create($log);
    }

    public static function logActivityLists()
    {
        return LoginActivityModel::latest()->get();
    }
}
