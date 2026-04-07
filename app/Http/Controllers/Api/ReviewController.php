<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Appointment;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    protected $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    /**
     * Список всех отзывов (например, для админа или общей страницы)
     */
    public function index()
    {
        $reviews = $this->service->getAll();
        return ReviewResource::collection($reviews);
    }

    public function show(Review $review)
    {
        return new ReviewResource($review->load(['appointment.user', 'appointment.doctor']));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        // 1. Проверяем права через Policy
        $this->authorize('update', $review);

        // 2. Обновляем данные (используем те же правила, что и при создании)
        $review->update($request->validated());

        return new ReviewResource($review);
    }

    /**
     * Оставить отзыв
     */
    public function store(StoreReviewRequest $request, Appointment $appointment)
    {
        // Проверка доступа через Policy
        $this->authorize('create', [Review::class, $appointment]);

        // Проверка на дубликат
        if ($appointment->reviews()->exists()) {
            return response()->json(['message' => 'Отзыв уже существует'], 422);
        }

        // Создаем отзыв, привязывая его к ID из URL
        $review = $this->service->create([
            'appointment_id' => $appointment->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['message' => 'Отзыв удален']);
    }
}
