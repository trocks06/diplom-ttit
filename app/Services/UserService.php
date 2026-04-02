<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService
{
    protected AvatarService $avatarService;

    public function __construct(User $user, AvatarService $avatarService)
    {
        parent::__construct($user);
        $this->avatarService = $avatarService;
    }

    public function store(array $data): User
    {
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $this->avatarService->upload($data['avatar']);
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->find($id);

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            if ($user->avatar) {
                $this->avatarService->delete($user->avatar);
            }
            $data['avatar'] = $this->avatarService->upload($data['avatar']);
        }

        $user->update($data);
        return $user;
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
