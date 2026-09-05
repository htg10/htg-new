<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Entry;
use App\Models\User;
use App\Notifications\BdmAssignedNotification;

class TestingController extends Controller
{
    public function index()
    {
        return view('text');
    }

    public function getCompanyData($name)
    {
        $company = Entry::where('company', 'like', '%' . $name . '%')->first();

        return response()->json($company);
    }

    public function chart()
    {
        $result = DB::select("select count(*) as total_entry, type from entries group by type");
        $charData = "";
        foreach ($result as $list) {
            // $charData .= "['" . $list->user_id . "', " . $list->total_user . "],";
            $charData .= "['" . $list->type . "', " . $list->total_entry . "],";
        }
        $arr['chartData'] = rtrim($charData, ',');
        // echo $charData;
        return view('piechart', $arr);
    }

    public function notify()
    {
        if (auth()->user()) {
            $user = User::first();
            $admin = auth()->user();
            auth()->user()->notify(new BdmAssignedNotification($user, $admin));
        }
        dd('done');
    }

}
