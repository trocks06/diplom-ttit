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
        $reviews = $this->service->getAll([
            'appointment.patient.user',
            'appointment.schedule.doctor.user'
        ]);
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
        $this->authorize('create', [Review::class, $appointment]);

        $review = $this->service->createForAppointment($appointment, $request->validated());

        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['message' => 'Отзыв удален']);
    }
}
