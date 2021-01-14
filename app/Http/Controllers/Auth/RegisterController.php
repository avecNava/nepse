<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Shareholder;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => Str::title($data['name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        
        //split name into first and last names
        $splitName = explode(' ', $data['name'], 2); // Restricts it to only 2 values   
        $first_name = $splitName[0];
        $last_name = '';
        foreach ($splitName as $key => $value) {
            if($key > 0 ){
                $last_name .= $value . ' ';
            }
        }
        $last_name = Str::of($last_name)->trim();
        
        //Perform login so that we can send customized email  for verification (user model)
        //or create a session for the new user, and use in user model sendEmailVerificationNotification()
        Auth::login($user);

        //create session record for the tenant_id (otherwise tenant_id will not be created for Shareholder)
        session()->put('tenant_id', $user->id);
        session()->put('shareholder_id', $user->id);

        //insert the user as shareholder
        Shareholder::create([
            'parent_id' => $user->id,
            'parent' => true,                   //all registered users will be the parent by default
            'first_name' => Str::ucfirst($first_name),
            'last_name' => Str::title($last_name),
            'email' => $data['email'],
            'uuid' => Str::uuid(),
            'last_modified_by' => $user->id,
        ]);
        
        //destroy the tenant_id session, only needed for adding shareholders data
        // session()->forget('tenant_id');
        
        return $user;
    }
}
