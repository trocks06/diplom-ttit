<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template TModel of Model
 */
abstract class BaseService
{
    /**
     * @var TModel
     */
    protected Model $model;

    /**
     * @param TModel $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Получить все записи с возможностью eager loading связей.
     *
     * @param array<string> $with
     * @return Collection
     */
    public function getAll(array $with = []): Collection
    {
        $query = $this->model->newQuery();
        if (!empty($with)) {
            $query->with($with);
        }
        return $query->get();
    }

    /**
     * @return TModel
     */
    public function find(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record->delete();
    }
}
