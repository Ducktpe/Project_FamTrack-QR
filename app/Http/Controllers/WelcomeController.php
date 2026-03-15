<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DistributionEvent;

class WelcomeController extends Controller
{
    public function index()
    {
        $events = DistributionEvent::whereIn('status', ['ongoing', 'upcoming'])
            ->orderByRaw("FIELD(status, 'ongoing', 'upcoming')")
            ->orderBy('event_date', 'asc')
            ->get();

        return view('welcome', compact('events'));
    }
}