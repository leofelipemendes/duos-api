<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use \Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use JWTAuth;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UsersController extends Controller {

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserValidator
     */
    protected $validator;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator) {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $users = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                        'data' => $users,
            ]);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function register(Request $request) {
        try {

            //$this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $params = [
                'name' => $request->usuario,
                'password' => bcrypt($request->senha),
                'email' => $request->email
            ];

            $user = $this->repository->create($params);

            $response = [
                'message' => 'User created.',
                'data' => $user->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                            'error' => true,
                            'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * efetua verificacao do login e retorna o token
     * @param Request $request
     */
    public function login(Request $request) {

        $credentials['name'] = $request->usuario;
        $credentials['password'] = $request->senha;
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
               return response()->json([
                   'status' => 'error',
                   'message' => 'Invalid credentials'
               ],401); 
            }
        } catch (JWTException $ex) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'could_not_create_token'], 500);
        }
         return response()->json([
                    'token' => $token
        ]);
    }

}
