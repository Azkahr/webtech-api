<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UploadRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $upload;

    public function __construct()
    {
        $this->upload = new UploadRepository();
    }
    
    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->access_token = $token;
            $user->token_type = 'Bearer';
            
            return response()->json([
                'message' => 'Login success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'required|min:3',
                'email' => 'required',
                'password' => 'required|min:5',
                'dob' => 'required',
                'phone_number' => 'required',
                'profile_picture' => 'required|max:1024|mimes:png,jpg,jpeg',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $request->all();
    
            $data['profile_picture'] = $this->upload->save($request->file('profile_picture'));
    
            $user = User::create($data);
    
            return response()->json([
                'message' => 'User registered',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request) {
        return response()->json([
            'message' => 'User data',
            'data' => $request->user()
        ]);
    }

    public function update(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'dob' => 'required',
                'phone_number' => 'required',
                'profile_picture' => 'max:1024|mimes:png,jpg,jpeg',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            unset($data['_method']);

            $profile_picture = auth()->user()->profile_picture;

            if($request->file('profile_picture')) {
                $profile_picture = $this->upload->update($profile_picture, $request->file('profile_picture'));
            }

            $data['profile_picture'] = $profile_picture;

            User::where('id', auth()->user()->id)->update($data);

            return response()->json([
                'message' => 'User profile updated',
                'data' => auth()->user()->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    } 
    
    public function logout(Request $request) {
        Auth::user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Logout sucess',
        ]);
    }
}
