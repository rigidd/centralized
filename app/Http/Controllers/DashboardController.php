<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request)
    {
        $request->user()->load('events');
        return Inertia::render('Dashboard');
    }
}
