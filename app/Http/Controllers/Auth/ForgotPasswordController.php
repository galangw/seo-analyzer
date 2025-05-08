<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->middleware('guest');
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset code to the given user via WhatsApp.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate request
        if ($request->has('email')) {
            $request->validate(['email' => 'required|email']);
            $field = 'email';
            $value = $request->email;
        } else {
            $request->validate(['phone_number' => 'required|string|regex:/^\d{10,15}$/']);
            $field = 'phone_number';
            $value = $request->phone_number;
        }

        // Find user by email or phone number
        $user = User::where($field, $value)->first();

        if (!$user) {
            return back()->withErrors([$field => 'We can\'t find a user with that information.']);
        }

        // Ensure user has a phone number for receiving the code
        if (!$user->phone_number) {
            return back()->withErrors([$field => 'No phone number is associated with this account.']);
        }

        // Generate token
        $token = Str::random(60);
        $code = $this->whatsAppService->generateVerificationCode();

        // Store the token and code
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Store the code with the user
        $user->verification_code = $code;
        $user->save();

        // Send code via WhatsApp
        $this->whatsAppService->sendPasswordResetCode($user->phone_number, $code);

        // Redirect to the reset form with token
        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
            ->with('status', 'We have sent your password reset code to your WhatsApp!');
    }
}
