<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function __constructor()
    {
        
    }

    public function index()
    {
        $categories = [
            'Complaint' => 'Complaints',
            'Suggestion'=>'Suggestions',
            'Feature'=>'New feature',
            'Issue'=>'Isues',
            'Help'=>'Help and Support',
            'Inquiry'=>'General Inquiry',
            'Others' =>'Others',
        ];
    
        return view('feedback', [
            'user' => Auth::user(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        return $request->name;
    }
}
