<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends BaseService<User>
 */
class UserService extends BaseService
{
    protected AvatarService $avatarService;

    public function __construct(User $user, AvatarService $avatarService)
    {
        parent::__construct($user);
        $this->avatarService = $avatarService;
    }

    public function create(array $data): User
    {
        $data['email_verified_at'] = now();
        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function updateAvatar(int $id, UploadedFile $file): string
    {
        $user = $this->find($id);
        if ($user->avatar) {
            $this->avatarService->delete($user->avatar);
        }
        $path = $this->avatarService->upload($file);
        $user->update(['avatar' => $path]);
        return $path;
    }

    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if ($user->avatar) {
            $this->avatarService->delete($user->avatar);
        }
        return $user->delete();
    }
}
