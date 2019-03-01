<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TesteCreateRequest;
use App\Http\Requests\TesteUpdateRequest;
use App\Repositories\TesteRepository;
use App\Validators\TesteValidator;

/**
 * Class TestesController.
 *
 * @package namespace App\Http\Controllers;
 */
class TestesController extends Controller
{
    /**
     * @var TesteRepository
     */
    protected $repository;

    /**
     * @var TesteValidator
     */
    protected $validator;

    /**
     * TestesController constructor.
     *
     * @param TesteRepository $repository
     * @param TesteValidator $validator
     */
    public function __construct(TesteRepository $repository, TesteValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $testes = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $testes,
            ]);
        }

        return view('testes.index', compact('testes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TesteCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function register(Request $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $teste = $this->repository->create($request->all());

            $response = [
                'message' => 'Teste created.',
                'data'    => $teste->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teste = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $teste,
            ]);
        }

        return view('testes.show', compact('teste'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teste = $this->repository->find($id);

        return view('testes.edit', compact('teste'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TesteUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TesteUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $teste = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Teste updated.',
                'data'    => $teste->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Teste deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Teste deleted.');
    }
}
