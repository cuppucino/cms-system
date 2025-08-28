<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['gownCollection', 'convocationSession']);

        // tiny summary the UI can use
        $summary = [
            'status'  => $user->status, // pending|registered|confirmed
            'session' => $user->convocationSession
                ? [
                    'id'   => $user->convocationSession->id,
                    'name' => $user->convocationSession->name,
                    'date' => $user->convocationSession->date,
                    'location' => $user->convocationSession->location,
                  ]
                : null,
            'gown' => $user->gownCollection
                ? [
                    'status' => $user->gownCollection->status, // reserved|collected|returned|late
                    'size'   => $user->gownCollection->size,
                  ]
                : null,
            // placeholders for now (wire later)
            'invitation' => false,
            'attended'   => false,
        ];

        return Inertia::render('student/Dashboard', [
            'studentSummary' => $summary,
        ]);
    }
}
