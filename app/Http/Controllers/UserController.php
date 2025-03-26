<?php

namespace App\Http\Controllers;

use App\Enums\UserResponses;
use App\Http\Requests\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->listUsers(
            $request->get('name'),
            $request->get('cpf'),
            $request->get('per_page', 10)
        );

        return response()->json(['data' => $users]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'message' => UserResponses::CREATED,
            'data' => $user,
        ], Response::HTTP_CREATED);
    }
}
