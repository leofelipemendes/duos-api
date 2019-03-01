<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use JWTAuth;

class UserController extends Controller {

    /**
     * Faz login e retorna o token
     * @param Request $request
     * @return type
     */
    public function login(Request $request) {

        $credentials['name'] = $request->get('usuario');
        $credentials['password'] = $request->get('senha');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                            'status' => 'error',
                            'message' => 'invalid credentials'
                                ], 401);
            }
        } catch (JWTException $ex) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
                    'token' => $token
        ]);
    }

    /**
     * Criacao basica de usuario
     * @param Request $request
     * @return type
     */
    public function register(Request $request) {

        $user = User::create([
                    'name' => $request->get('usuario'),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->get('senha'))
        ]);

        return response()->json(compact('user'));
    }

    public function getAuthenticatedUser() {
        try {
            if (!$user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user not found'], 400);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

}
