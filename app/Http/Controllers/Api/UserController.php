<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll();
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['patient', 'doctor']));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Профиль обновлен',
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user->id);
        return response()->json([
            "message" => "Аккаунт успешно удален."
        ]);
    }

    public function me()
    {
        return new UserResource(auth()->user());
    }



    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $path = $this->userService->updateAvatar(auth()->id(), $request->file('avatar'));
        return response()->json([
            'message' => 'Аватар успешно обновлен',
            'avatar_url' => $path
        ]);
    }

    public function deleteProfile()
    {
        $user = auth()->user();
        if ($user->role->role_name !== 'Пациент') {
            return response()->json(['message' => 'Только пациент может удалить свой профиль.'], 403);
        }
        if ($user->patient->appointments()->whereHas('status', fn($q) => $q->where('status_name', 'Запланирован'))->exists()) {
            return response()->json(['message' => 'У вас есть активные записи.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Профиль удалён.']);
    }
}
