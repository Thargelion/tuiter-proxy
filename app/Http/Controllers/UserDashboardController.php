<?php

namespace App\Http\Controllers;

use App\Models\ApplicationToken;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
            'tokens' => ApplicationToken::where('user_id', $request->user()->id)->get()
        ]);
    }
}
