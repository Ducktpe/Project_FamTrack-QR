<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HouseholdController extends Controller
{
    public function create()
    {
        return view('households.create'); // we’ll create this Blade file next
    }
}
