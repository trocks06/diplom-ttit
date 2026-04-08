<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll(['role']);
        return UserResource::collection($users);
    }

    public function me()
    {
        $user = auth()->user();
        $user->load(['role', 'patient', 'doctor']);
        return new UserResource($user);
    }

    public function show(User $user)
    {
        $user->load(['role', 'patient', 'doctor']);
        return new UserResource($user);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            auth()->user(),
            $request->validated()
        );
        $user->load(['role', 'patient', 'doctor']);
        return response()->json([
            'message' => 'Профиль обновлён',
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user->id);
        return response()->json([
            "message" => "Аккаунт успешно удалён."
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $path = $this->userService->updateAvatar(auth()->id(), $request->file('avatar'));
        return response()->json([
            'message' => 'Аватар успешно обновлён',
            'avatar_url' => $path
        ]);
    }

    /**
     * Удаление своего аккаунта (для всех ролей)
     */
    public function deleteMe(): JsonResponse
    {
        try {
            $this->userService->deleteSelf(auth()->user());
            return response()->json(['message' => 'Ваш профиль удалён.']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
