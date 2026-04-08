<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

    /**
     * Удаление текущего пользователя с проверками по ролям.
     * @throws ValidationException
     */
    public function deleteSelf(User $user): void
    {
        $role = $user->role?->role_name;

        if ($role === 'Пациент') {
            $hasActive = $user->patient?->appointments()
                ->whereHas('status', fn($q) => $q->where('status_name', 'Запланирован'))
                ->exists();
            if ($hasActive) {
                throw ValidationException::withMessages([
                    'user' => ['У вас есть активные записи. Сначала отмените их.'],
                ]);
            }
        }

        if ($role === 'Врач') {
            $hasUpcoming = $user->doctor?->schedules()
                ->where('start_time', '>', now())
                ->whereHas('appointments', fn($q) =>
                $q->whereHas('status', fn($s) => $s->where('status_name', 'Запланирован'))
                )
                ->exists();
            if ($hasUpcoming) {
                throw ValidationException::withMessages([
                    'user' => ['У вас есть предстоящие приёмы. Невозможно удалить профиль.'],
                ]);
            }
        }

        if ($role === 'Администратор') {
            $adminCount = User::whereHas('role', fn($q) => $q->where('role_name', 'Администратор'))->count();
            if ($adminCount <= 1) {
                throw ValidationException::withMessages([
                    'user' => ['Нельзя удалить последнего администратора.'],
                ]);
            }
        }

        $this->delete($user->id);
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
