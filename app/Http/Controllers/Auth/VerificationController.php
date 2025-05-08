<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $whatsAppService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->middleware('guest')->except(['verify', 'resend', 'show']);
        $this->middleware('auth')->only(['verify', 'resend', 'show']);
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show the verification notice view.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.verify');
    }

    /**
     * Verify the user's phone number with a verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user->verification_code == $request->verification_code) {
            $user->is_verified = true;
            $user->verification_code = null;
            $user->save();

            return redirect($this->redirectTo)
                ->with('success', 'Phone number verified successfully!');
        }

        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    /**
     * Resend the verification code to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $user = Auth::user();
        
        if ($user->is_verified) {
            return redirect($this->redirectTo)
                ->with('success', 'Your phone number is already verified.');
        }

        // Check if phone number exists
        if (!$user->phone_number) {
            return back()->withErrors(['phone_number' => 'Phone number is not set. Please update your profile first.']);
        }

        $verificationCode = $this->whatsAppService->generateVerificationCode();
        $user->verification_code = $verificationCode;
        $user->save();

        $this->whatsAppService->sendVerificationCode($user->phone_number, $verificationCode);

        return back()->with('success', 'Verification code has been resent to your WhatsApp.');
    }
}
