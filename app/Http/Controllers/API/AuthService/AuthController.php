<?php
namespace App\Http\Controllers\API\AuthService;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
    {
    public function getUser(Request $request)
        {
        return response()->json($request->user());
        }


    public function register(Request $request)
        {
        // return "a";
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed', // add 'confirmed' for password confirmation
        // ]);
        // return "a";
        // Create a new user

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate token
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
        }
    public function login(Request $request)
        {
        // // Validate the request
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Optionally, you can generate a token for the user (if using API tokens)
            $token = $user->createToken('YourAppName')->accessToken;

            // Return response with user data and token
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
            }

        // Return error response if login fails
        return response()->json(['message' => 'Invalid credentials.'], 401);
        }


    // Method to logout and destroy session
    public function logout(Request $request)
        {
        // Invalidate the user's session
        auth()->logout();

        // Optionally, you can clear the token or session data if needed
        return response()->json(['message' => 'Successfully logged out']);
        }


    // Method to check session
    public function checkSession(Request $request)
        {
        if ($request->session()->has('user')) {
            return response()->json(['user' => session('user')]);
            }

        return response()->json(['error' => 'No active session'], 401);
        }


    public function changePassword(Request $request)
        {
        // Validate the request
        // $request->validate([
        //     'current_password' => 'required',
        //     'new_password' => 'required|min:6|confirmed', // Minimum 6 characters and confirm password
        // ]);

        $user = Auth::user();

        // Check if the current password is correct
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 401);
            }

        // Change the password
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.'], 200);
        }
    }
