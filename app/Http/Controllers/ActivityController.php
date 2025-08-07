<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Activities")
 */
class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/activities",
     *     tags={"Activities"},
     *     summary="Получить все деятельности в бд",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Список деятельностей", @OA\JsonContent()),
     *     @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="",
     *            example="15",
     *            @OA\Schema(type="integer")
     *       ),
     *     @OA\Parameter(
     *            name="page",
     *            in="query",
     *            description="",
     *            example="1",
     *            @OA\Schema(type="integer")
     *       ),
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer'
        ]);

        return ActivityResource::collection(Activity::with(['parent', 'children'])->paginate($validated['per_page'] ?? 15));
    }

    /**
     * @OA\Post(
     *     path="/activities",
     *     tags={"Activities"},
     *     summary="Создать новую активность",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Строительство"),
     *             @OA\Property(property="parent_id", type="integer", example="null")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Деятельность создана", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Превышен максимальный уровень вложенности (3)")
     * )
     */
    public function store(ActivityRequest $request)
    {
        $vaildated = $request->validated();

        if ($vaildated['parent_id']) {
            if (! Activity::find($vaildated['parent_id'])->canHaveChildren()) return response()->json(['message' => 'Превышен максимальный уровень вложенности (3).'], 422);
        }

        $activity = Activity::create($vaildated);
        return new ActivityResource($activity);
    }

    /**
     * @OA\Put(
     *     path="/activities/{id}",
     *     tags={"Activities"},
     *     summary="Обновить деятельность",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Автомобили"),
     *             @OA\Property(property="parent_id", type="integer", example="null")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Деятельность обновлена", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Превышен максимальный уровень вложенности (3)"),
     *     @OA\Response(response=404, description="Деятельность не найдена")
     * )
     */
    public function update(ActivityRequest $request)
    {
        $validated = $request->validated();

        try {
            $activity = Activity::findOrFail($request->id ?? null);
        } catch (\Exception $e) {
            // model not found
            return response()->json(['message' => 'Деятельность не найдена'], 404);
        }

        if ($validated['parent_id']) {
            if (! Activity::find($validated['parent_id'])->canHaveChildren()) return response()->json(['message' => 'Превышен максимальный уровень вложенности (3).'], 422);
        }

        $activity->update($validated);

        return new ActivityResource($activity);
    }

    /**
     * @OA\Delete(
     *     path="/activities/{id}",
     *     tags={"Activities"},
     *     summary="Удалить деятельность",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Деятельность удалена", @OA\JsonContent())
     * )
     */
    public function destroy(Request $request)
    {
        $activity = Activity::find($request->id ?? null);
        if ($activity) $activity->delete();

        return response()->json(['message' => 'Удалено']);
    }
}

