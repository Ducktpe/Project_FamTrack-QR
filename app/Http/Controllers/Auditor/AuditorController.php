<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Household;

class AuditorController extends Controller
{
    public function familyProfiles()
    {
        $households = Household::paginate(25);
        return view('auditor.family-profiles', compact('households'));
    }
}