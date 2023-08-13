<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Default sort by id asc
        $sortArray = $request->sort ? explode("-", $request->sort) : ['id', 'asc'];
        
        // Get all users 
        $users = UserResource::collection(
            User::
                when($request->has('count') && $request->has('offset'), function ($query) use ($request) {
                    return $query->skip($request->offset ?: 0)->take($request->count);
                })
                ->orderBy($sortArray[0], $sortArray[1])->get()
        );   
        $total = User::all()->count();

        return response()->json([                               
            'data'=> $users,
            'total' => $total
        ]);  
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'  
        ]);
        if ($validator->fails()) {
            return errorResponse(100, $validator->errors()->first(), 400);                   
        }    

        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return errorResponse(50, 'Invalid Credentials', 401);           
        }

        $user = auth()->user();

        $user = User::where('email', $request->email)->first();

        // Implementing Personal Access Token
        $token = $this->generatePersonalAccessToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    private function generatePersonalAccessToken($user)
    {
        $tokenName = 'Personal Access Token';
        // Scope is used to set access permission that differentiates between admin and user
        $scopes = $user && $user->role === User::ROLE_ADMIN ? ['users-manage-all'] : ['users-manage-self'];

        return $user->createToken($tokenName, $scopes)->accessToken;
    }
}

