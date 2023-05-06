<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $language = $request->get('language');
        // cookie expires in 1 month = 48800 minutes
        setcookie("language", $language, time() + (86400 * 30), "/"); // 86400 = 1 day
        return redirect()->back();
    }
}
