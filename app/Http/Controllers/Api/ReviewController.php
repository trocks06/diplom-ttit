<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        return ReviewResource::collection($this->service->all(['appointment.user', 'appointment.doctor']));
    }

    /**
     * Оставить отзыв
     */
    public function store(StoreReviewRequest $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Проверяем через Policy: мой ли это прием и закончен ли он
        $this->authorize('create', [Review::class, $appointment]);

        // Проверяем, нет ли уже отзыва к этому приему
        if ($appointment->review()->exists()) {
            return response()->json(['message' => 'Отзыв к этому приему уже оставлен'], 422);
        }

        $review = $this->service->create($request->validated());
        return new ReviewResource($review);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return response()->json(['message' => 'Отзыв удален']);
    }
}
