<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Add this import for Log
use App\Http\Controllers\SMS\SMSController;
use App\Models\Staff;
use App\Models\UsrUserLog;
use App\Services\StoreContext;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Mail\SendPasswordMail;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function frontend(){
        return view('authentication.frontend-login');
    }

    public function Register()
    {
        return view('authentication.register');
    }

    public function RegisterAccount(Request $request)
    {
        // Validate Input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:10,15|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'ghana_card' =>  'required',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ], [
            'confirm_password.same' => 'The password confirmation does not match.',
            'confirm_password.required' => 'Please confirm your password.',
        ]);

            $insertCat = new User();
            $insertCat->name = trim($request->name);
            $insertCat->email = $request->email;
            $insertCat->phone = $request->phone; 
            $insertCat->ghana_card = $request->ghana_card; 
            $insertCat->status = "Active";  
            $insertCat->password = Hash::make($request->password); 
            
            $insertCat = $insertCat->save();

              return $insertCat ? back()->with('message_success','Account successfully created') : back()->with('message_error','Something went wrong, please try again.');
           
         
    }

    //Login 
    public function loginAccount(Request $request)
    {
         $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    $maxAttempts = 5; // maximum login attempts
    $lockMinutes = 10; // minutes account will be locked

    $user = User::where('email', $request->email)
                ->where('user_cat', 2)
                ->first();

    if (!$user) {
        return back()->with('message_error', 'Invalid email or password');
    }

    // Check if account is locked
    if ($user->locked_until && Carbon::now()->lt($user->locked_until)) {

    $secondsLeft = Carbon::now()->diffInSeconds($user->locked_until);
    $minutesLeft = ceil($secondsLeft / 60);

    return back()->with(
        'message_error',
        "Account locked. Try again in {$minutesLeft} minute(s)."
    );
    }

    // Check password
    if (!Hash::check($request->password, $user->password)) {

        $user->login_attempts += 1;

        $attemptsLeft = $maxAttempts - $user->login_attempts;

        if ($user->login_attempts >= $maxAttempts) {

            $user->locked_until = Carbon::now()->addMinutes($lockMinutes);
            $user->login_attempts = 0;
            $user->save();

            return back()->with(
                'message_error',
                "Too many failed attempts. Your account is locked for {$lockMinutes} minutes."
            );
        }

        $user->save();

        return back()->with(
            'message_error',
            "Invalid email or password. {$attemptsLeft} attempt(s) left before account lock."
        );
    }

    if (($user->status ?? 'Active') !== 'Active') {
        return back()->with(
            'message_error',
            'Your account has been blocked. Contact your administrator.'
        );
    }

    // Successful login → reset attempts
    $user->login_attempts = 0;
    $user->locked_until = null;
    $user->save();

    Auth::login($user);

    return redirect()->route('applicant-dashboard')
        ->with('message_success', 'Login successful');
    }
     
    public function logout()
    {
        session()->flush();
        return redirect('/')->with('message_success', 'Logged out successfully');
    }

   public function getForgetPassword()
   {
    return view('authentication.forgot-password');
   }

   public function forgotPass(Request $request)
   {

 
     $request->validate([
            'email' => 'required|email',
           
        ]);


        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('message_error', 'Email not found');
        }

        // Generate 6 digit OTP
        $otp = rand(1000, 9999);

        $user->reset_otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();
        
       // Data to send in email
    $data = [
        'otp' => $otp,
        'name' => $user->name,
         'email' => $user->email
    ];

      // Send Email (try-catch recommended)
    try {
        Mail::to($user->email)->send(new SendMail($data));
    } catch (\Exception $e) {
        return back()->with('message_error', 'Failed to send OTP. Please try again.');
    }

    // Redirect to OTP verification page (use user id for safety)
    return redirect()->route('verify-otp-page', Crypt::encrypt($user->id))
                     ->with('message_success', 'OTP sent to your email! ');
          
        
        
   }

     
 

    public function getOtp($id)
    {
         $decodeId = Crypt::decrypt($id);
        $data =  User::find($decodeId);
        return view('authentication.verify-otp-page',['id'=>$id,'data'=>$data]);
    }

    //Show Admin Login Page 
    public function getAdminLoginPage()
    {
        return view('authentication.admin-login');
    }

     public function authenticationProcess(Request $request){

        $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $maxAttempts = 5;
    $lockMinutes = 10;

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->with('login_error_message','Email does not exist or wrong password.');
    }

    // Check if account is locked
    if ($user->locked_until && Carbon::now()->lt($user->locked_until)) {

        $secondsLeft = Carbon::now()->diffInSeconds($user->locked_until);
        $minutesLeft = ceil($secondsLeft / 60);

        return back()->with(
            'login_error_message',
            "Account locked. Try again in {$minutesLeft} minute(s)."
        );
    }

    // Check password
    if (!Hash::check($request->password, $user->password)) {

        $user->login_attempts += 1;

        $attemptsLeft = $maxAttempts - $user->login_attempts;

        if ($user->login_attempts >= $maxAttempts) {

            $user->locked_until = Carbon::now()->addMinutes($lockMinutes);
            $user->login_attempts = 0;
            $user->save();

            return back()->with(
                'login_error_message',
                "Too many failed attempts. Account locked for {$lockMinutes} minutes."
            );
        }

        $user->save();

        return back()->with(
            'login_error_message',
            "Wrong password. {$attemptsLeft} attempt(s) left before account lock."
        );
    }

    if (($user->status ?? 'Active') !== 'Active') {
        return back()->with(
            'login_error_message',
            'Your account has been blocked. Contact your administrator.'
        );
    }

    // Login successful
    Auth::login($user);

    $request->session()->regenerate();

    // Reset attempts
    $user->login_attempts = 0;
    $user->locked_until = null;
    $user->save();

    // Insert login log
    $insertLogs = new UsrUserLog();
    $insertLogs->user_id = Auth::user()->id;
    $insertLogs->login_date = Carbon::now();
    $insertLogs->login_ip = request()->ip();
    $insertLogs->save();

    session()->put('userLogId', $insertLogs->id);

    $storeContext = app(StoreContext::class);

    if ($storeContext->hasGlobalStoreAccess($user)) {
        $storeContext->clearActiveStore();

        return redirect()->intended('dashboard');
    }

    $mappedStoreIds = $storeContext->getMappedStoreIds($user);

    if (empty($mappedStoreIds)) {
        Auth::logout();

        return back()->with('login_error_message', 'No store assigned. Contact administrator.');
    }

    if (count($mappedStoreIds) === 1) {
        $storeContext->setActiveStore($mappedStoreIds[0]);

        return redirect()->intended('dashboard');
    }

    return redirect()->route('choose-store');
        
    }

    public function getChooseStoreView()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('admin-login');
        }

        $storeContext = app(StoreContext::class);

        if ($storeContext->hasGlobalStoreAccess($user)) {
            return redirect()->route('dashboard');
        }

        $mappedStores = $storeContext->getMappedStores($user);

        if ($mappedStores->isEmpty()) {
            Auth::logout();

            return redirect()->route('admin-login')
                ->with('login_error_message', 'No store assigned. Contact administrator.');
        }

        if ($mappedStores->count() === 1) {
            $storeContext->setActiveStore($mappedStores->first()->id);

            return redirect()->route('dashboard');
        }

        $userCat = \App\Models\UserCat::find($user->user_cat);

        return view('authentication.choose-store', [
            'mappedStores' => $mappedStores,
            'userRole' => $userCat->cat_name ?? 'User',
            'storeCount' => $mappedStores->count(),
            'centralCount' => $mappedStores->where('store_group', 'central')->count(),
            'satelliteCount' => $mappedStores->where('store_group', 'satellite')->count(),
        ]);
    }

    public function selectStoreProcess(Request $request)
    {
        $request->validate([
            'store_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $storeContext = app(StoreContext::class);

        if (!$user || $storeContext->hasGlobalStoreAccess($user)) {
            return redirect()->route('dashboard');
        }

        $storeId = (int) $request->store_id;

        if (!$storeContext->canAccessStore($user, $storeId)) {
            return back()->with('message_error', 'You are not assigned to this store.');
        }

        $storeContext->setActiveStore($storeId);

        return redirect()->route('dashboard')
            ->with('message_success', 'Store selected successfully.');
    }

    public function switchStore()
    {
        $user = Auth::user();
        $storeContext = app(StoreContext::class);

        if (!$user || $storeContext->hasGlobalStoreAccess($user)) {
            return redirect()->route('dashboard');
        }

        $storeContext->clearActiveStore();

        return redirect()->route('choose-store');
    }

    public function getUserProfile()
    {

       $user = Staff::where('staff_id',Auth::user()->staff_id)->first();
        return view('user-management.user-profile',['user'=>$user]);
    }


    //Update User Profile Photo
        public function updatePhoto(Request $request)
    {
        $staff = Staff::where('staff_id', $request->staff_id)->first();

        if(!$staff){
            return back()->with('error_message','Staff not found');
        }

        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;

            $file->move(public_path('uploads/profile-photo'), $filename);

            $staff->picture = 'uploads/profile-photo/'.$filename;
        }

        $staff->updated_by = Auth::user()->id;

        $status = $staff->save();

        return $status 
            ? back()->with('message_success','Photo updated successfully') 
            : back()->with('error_message','Something went wrong, please try again.');
    }

    //Updating User Password

     public function updatePassword(Request $request){
       $request->validate([
              'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'The password confirmation does not match.',
            'confirm_password.required' => 'Please confirm your password.',
        ]);

         $user = Auth::user();

    // Check if the old password matches
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error_message', 'Current password is incorrect.');
    }

    // Update new password
    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    return back()->with('message_success', 'Password changed successfully.');
}
   

  public function resetOtp(Request $request, $id)
  {
  
      // Join the OTP fields
    $otp = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4;

    // Decrypt the user ID

    $decodeID = Crypt::decrypt($id);
   
    $user = User::find($decodeID);

    if (!$user) {
        return back()->with('error_message', 'User not found.');
    }

    // Check if OTP matches and is not expired
    if ($user->reset_otp != $otp) {
        return back()->with('error_message', 'Invalid OTP.');
    }

    if (Carbon::now()->gt($user->otp_expires_at)) {
        return back()->with('error_message', 'OTP has expired.');
    }

    // Generate new random password with special characters
    $newPassword = $this->generateRandomPassword(8);

    // Update password in database (hashed)
    $user->password = Hash::make($newPassword);
    $user->reset_otp = null;           // Clear OTP
    $user->otp_expires_at = null;      // Clear expiry
    $user->save();

    // Send new password via email
    $data = [
        'password' => $newPassword,
        'name' => $user->name,
        'email' => $user->email,
    ];

    Mail::to($user->email)->send(new SendPasswordMail($data));

   return redirect('/')->with('message_success', 'Password changed successfully. Check your email for the new password.');
  }

  //Generate 8 randome characters
    function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
