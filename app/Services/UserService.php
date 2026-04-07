<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update(array_intersect_key($data, array_flip([
                'firstname', 'lastname', 'patronymic', 'phone', 'email'
            ])));
            if ($user->role->role_name === 'Пациент' && $user->patient) {
                $user->patient->update(array_intersect_key($data, array_flip([
                    'address', 'gender', 'allergies', 'chronic_diseases', 'birth_date'
                ])));
            }

            return $user->load(['patient', 'doctor']);
        });
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
