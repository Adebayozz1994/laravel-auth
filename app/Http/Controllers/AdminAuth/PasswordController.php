<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
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
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!Hash::check($value, $request->user()->password)) {
                    return $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // return back()->with('status', 'password-updated');
        return response()->json(['status' => 'Password updated successfully!'], 200);

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



    public function updatePassword(Request $request)
    {
        // Step 1: Validate the token first
        $admin = Admin::where('token', $request->token)->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // Step 2: Validate the password
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|confirmed|min:8', // Validate new password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Step 3: Update the password
        $admin->update([
            'password' => Hash::make($request->password) // Hash the new password
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}


