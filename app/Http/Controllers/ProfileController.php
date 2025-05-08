<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\WhatsAppService;

class ProfileController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->middleware('auth');
        $this->whatsAppService = $whatsAppService;
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Name has been updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'verification_code' => 'required|string',
        ]);

        // Verify the code
        $user = Auth::user();
        if ($user->verification_code !== $request->verification_code) {
            return redirect()->back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->email = $request->email;
        $user->verification_code = null; // Reset the code after successful verification
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Email has been updated successfully.');
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . Auth::id(),
            'verification_code' => 'required|string',
        ]);

        // Verify the code
        $user = Auth::user();
        if ($user->verification_code !== $request->verification_code) {
            return redirect()->back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->phone_number = $request->phone_number;
        $user->verification_code = null; // Reset the code after successful verification
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Phone number has been updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'verification_code' => 'required|string',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        // Verify the code
        if ($user->verification_code !== $request->verification_code) {
            return redirect()->back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->password = Hash::make($request->password);
        $user->verification_code = null; // Reset the code after successful verification
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password has been updated successfully.');
    }

    public function requestVerificationCode(Request $request)
    {
        $type = $request->input('type');
        $user = Auth::user();
        
        // Phone number is required to send WhatsApp messages
        if (empty($user->phone_number)) {
            return redirect()->back()->withErrors(['verification_code' => 'You need a phone number to receive verification codes.']);
        }
        
        // Generate a 6-digit verification code
        $verificationCode = $this->whatsAppService->generateVerificationCode();
        
        // Save the verification code
        $user->verification_code = $verificationCode;
        $user->save();
        
        // Send the verification code via WhatsApp
        $result = $this->whatsAppService->sendVerificationCode($user->phone_number, $verificationCode);
        
        if ($result) {
            return redirect()->back()->with('success', 'Verification code has been sent to your WhatsApp number.')
                                     ->with('code_type', $type);
        } else {
            return redirect()->back()->withErrors(['verification_code' => 'Failed to send verification code. Please try again.']);
        }
    }
} 