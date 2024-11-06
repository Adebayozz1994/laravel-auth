<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function updateAdminPassword(Request $request): RedirectResponse {
        $admin = auth()->guard('admin')->user();
        if(password_verify($request->current_password, $admin->password)){
            $validation = Validator::make($request->all(),[
                'current_password' => ['required'],
                'password' => ['required','same:password_confirmation', 'min:8']
            ]);
            if($validation->fails()){
                return Redirect::route('admin.profile.edit')->with('errors', $validation->errors());
            }else{
                Admin::where('id',$admin->id)->update([
                    'password' => Hash::make($request->password)
                ]);
                return Redirect::route('admin.profile.edit')->with('status', 'password-updated');
            }
        }else{
            return Redirect::route('admin.profile.edit')->with('status', 'password-not-verified');
        }
       
    }
}
