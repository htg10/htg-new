<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;

class CachesController extends Controller
{
    public function caches()
    {
        Artisan::call('config:cache');
        echo "Config Cached! <br>";

        Artisan::call('route:clear');
        echo "Route Cleared! <br>";

        Artisan::call('view:clear');
        echo "View Cleared! <br>";

        Artisan::call('cache:clear');
        echo "Application Cache Cleared! <br>";

        Artisan::call('send:expiry');
        echo "Send Expiry! <br>";

        Artisan::call('send:expired');
        echo "Send Expired! <br>";

        Artisan::call('view:clear');
        echo "View Cleared! <br>";

        Artisan::call('schedule:work');
        echo "Schedule Run";

    }
}
