<?php

namespace App\Http\Controllers;

use App\Enums\UserResponses;
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
        $users = $this->userService->listUsers($request->get('name'), $request->get('cpf'));

        return response()->json(['data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'birth_date' => 'required|date_format:Y-m-d',
            'email' => 'required|email|unique:users,email',
            'google_id' => 'required|string|unique:users,google_id',
        ]);

        $user = $this->userService->createUser($validatedData);

        return response()->json([
            'message' => UserResponses::CREATED,
            'data' => $user,
        ], Response::HTTP_CREATED);
    }
}
