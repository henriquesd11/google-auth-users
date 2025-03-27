<?php

namespace App\Http\Controllers;

use App\Enums\UserResponses;
use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class UserController
 *
 * Controlador responsável por gerenciar usuários.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Serviço de usuários.
     *
     * @var UserService
     */
    private UserService $userService;

    /**
     * Construtor do UserController.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Lista os usuários.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->listUsers(
            $request->get('name'),
            $request->get('cpf'),
            $request->get('per_page', 10)
        );

        return response()->json(['data' => $users]);
    }

    /**
     * Cria um novo usuário.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'message' => UserResponses::CREATED,
            'data' => $user,
        ], Response::HTTP_CREATED);
    }
}
