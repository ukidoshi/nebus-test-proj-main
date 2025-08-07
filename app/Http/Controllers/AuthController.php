<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;


/**
 * @OA\Tag(name="Auth")
 */
class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="Регистрация",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "name", "password"},
     *             @OA\Property(property="email", type="email", example="email@example.com"),
     *             @OA\Property(property="name", type="string", example="Nachyn"),
     *             @OA\Property(property="password", type="password", example="Password123"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Пользователь создан", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Произошла ошибка")
     * )
     */
    public function register(AuthRegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = User::create($validated);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка: регистрация'], 422);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Логин",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="email", example="email@example.com"),
     *             @OA\Property(property="password", type="password", example="Password123"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Успех", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Провал")
     * )
     */
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        if (!auth()->attempt($validated)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        return response()->json([
            'token' => auth()->user()->createToken('auth-token')->plainTextToken
        ]);
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     tags={"Auth"},
     *     summary="Получить инфу о пользователе",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Успех", @OA\JsonContent())
     * )
     */
    public function me()
    {
        return response()->json(['email' => auth()->user()->email]);
    }
}
