<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use App\Services\UtilityService;
use App\Mail\FeedbackMail;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function __constructor()
    {
        
    }

    public function index()
    {
        $categories = [
            'complaint' => 'Complaints',
            'suggestion'=>'Suggestions',
            'feature'=>'New feature',
            'issue'=>'Isues',
            'help'=>'Help and Support',
            'inquiry'=>'General Inquiry',
            'others' =>'Others',
        ];
    
        return view('feedback', [
            'user' => Auth::user(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:25|min:3',
            'email' => 'required|email',
            'category' => 'required||in:complaint,suggestion,feature,issue,help,inquiry,others',
            'title' => 'nullable|min:10',
            'feedback' => 'required|min:10',
            'attachment' => 'nullable|mimes:jpeg,jpg,png',
        ],
        [ 'category.*' => 'Please choose a valid category']);
        
        $file_name_str='';

        //file upload
        if($request->file('attachment')){
            
            $destinationPath = storage_path('app/feedbacks');
            $file_name = $request->file('attachment')->getClientOriginalName();
            $extension = $request->file('file')->extension();      //retuns txt for html , sql files...            
            //get the file extension from filename manually
            // $tmp = explode('.', $file_name);
            // $extension = $tmp[ sizeof($tmp)-1 ];

            $file_name_str = UtilityService::serializeTime() .'-'. UtilityService::serializeString($request->name) . '.' . $extension;
            $request->file('attachment')->move($destinationPath, $file_name_str);
        }

        $feedback = new Feedback();
        $feedback->id = $request->id;
        $feedback->name = $request->name;
        $feedback->email = $request->email;
        $feedback->category = $request->category;
        $feedback->title = $request->title;
        $feedback->description = $request->feedback;
        $feedback->attachment = $file_name_str;
        $feedback->save();

        $user = \App\Models\User::find(1);
        Mail::to($user)->send(new FeedbackMail($feedback));

        return redirect()->back()->with('message', 'Your message has been recorded.');
    }

    public function feedback(int $id)
    {
        $feedback = Feedback::find($id);
        return $feedback->toJSON();
    }

}
