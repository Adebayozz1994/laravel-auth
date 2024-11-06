<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        if (Auth::check()) {
            return response()->json([
                'status' => true,
                'message' => 'User is already logged in.',
                'user' => Auth::user(),
            ], 200);
        }    
        try {
            // Attempt to authenticate the user
            $request->authenticate();
    
            // Regenerate session to prevent session fixation
            $request->session()->regenerate();
    
            // Return a success response with the authenticated user
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'user' => Auth::user(),
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Login failed',
                'errors' => $e->errors(),
            ], 422); 
    
        } 
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['status' => true, 'message' => 'Successfully logged out.']);
    }



    public function uploadPicture(Request $req)
    {
    // Validate the uploaded file
    $req->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:8048', // Only accept images under 2MB
    ]);

    // Retrieve the uploaded file and the user ID
    $file = $req->file('profile_picture');
    $userId = Auth::id();

    // Create a unique name for the profile picture
    $newProfilePicture = time() . $userId . '.' . $file->getClientOriginalExtension();

    // Store the file in the "public/profile_pictures" directory
    $storePicture = $file->storeAs('/profile_picture', $newProfilePicture,'public');

    if ($storePicture) {
        // Update the user profile picture path in the database
        $updateProfilePic = User::where('id', $userId)->update([
            'profile_picture' => $newProfilePicture,
        ]);

        // Check if the update was successful
        if ($updateProfilePic) {
            return redirect('/dashboard')->with([
                'message' => 'Profile picture updated successfully!',
                'status' => true,
            ]);
        } else {
            return redirect('/dashboard')->with([
                'message' => 'Failed to update profile picture in database.',
                'status' => false,
            ]);
        }
    }

    // Fallback error message if picture couldn't be stored
    return redirect('/dashboard')->with([
        'message' => 'Failed to upload profile picture. Please try again.',
        'status' => false,
    ]);
}

}
