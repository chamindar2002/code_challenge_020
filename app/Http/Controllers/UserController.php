<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Add new User
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|max:100',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|confirmed',
            'medium_integration_token' => 'required|min:50'
        ]);

        try{

            $user = new User();
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = Hash::make($request->password);
            $user->medium_integration_token = $request->medium_integration_token;

            if ($user->save()) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'User Created Successfully'
                    ]
                );
            }

        }catch (\Exception $e) {

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
