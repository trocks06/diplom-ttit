<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
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

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['patient']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->userService->update($user->id, $request->validated());
        return new UserResource($updatedUser);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $path = $this->userService->updateAvatar(auth()->id(), $request->file('avatar'));
        return response()->json([
            'message' => 'Аватар успешно обновлен',
            'avatar_url' => $path
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
}
