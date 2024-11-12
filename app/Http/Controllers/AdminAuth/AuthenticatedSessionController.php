<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Admin;
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
        return view('adminauth.login');
    }

    public function getAdmin(Request $request){
      $id = substr($request->token, -1);
          $admin = Admin::where('id', $id)->first();
        if($request->token === $admin->token){
          return response()->json([
            'status' => true,
            'admin' => $admin
          ]);
        } else{
          return response()->json([
            'status' => false,
            'message' => 'Please login again'
          ]);
        }
      
    }
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
      $credentials = $request ->only('email','password');
      if(Auth::guard('admin')->attempt($credentials)) {
        $admin = Auth::guard('admin')->user();
        $token = time().$admin->id;
        Admin::where('email', $admin->email)->update([
          'token' => $token
        ]);
        // return redirect('/admin/dashboard');
        return response()->json([
          'status' => true,
          'token' => $token,
          'role' => $admin->role,
          
        ]);
      } else {
        return response()->json([
          'status' => false,
          'error' => 'The provided credentials do not match our records.',
        ]);
      }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Auth::guard('admin')->logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        // return redirect('/admin/login');
        $id = substr($request->token, -1);
         Admin::where('id',$id)->update([
            'token' => time().$id
         ]);
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
        $updateProfilePic = Admin::where('id', $userId)->update([
            'profile_picture' => $newProfilePicture,
        ]);

        // Check if the update was successful
        if ($updateProfilePic) {
            return redirect('/admin/dashboard')->with([
                'message' => 'Profile picture updated successfully!',
                'status' => true,
            ]);
        } else {
            return redirect('/admin/dashboard')->with([
                'message' => 'Failed to update profile picture in database.',
                'status' => false,
            ]);
        }
    }

    // Fallback error message if picture couldn't be stored
    return redirect('/admin/dashboard')->with([
        'message' => 'Failed to upload profile picture. Please try again.',
        'status' => false,
    ]);
}

}
