<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Buildings")
 */
class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/buildings",
     *     tags={"Buildings"},
     *     summary="Получить все здания в бд",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Список зданий", @OA\JsonContent()),
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

        return BuildingResource::collection(Building::query()->paginate($validated['per_page'] ?? 15));
    }

    /**
     * @OA\Post(
     *     path="/buildings",
     *     tags={"Buildings"},
     *     summary="Добавить новое здание",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"address", "lat", "lng"},
     *             @OA\Property(property="address", type="string", example="1-й Зачатьевский пер., д.6ст1"),
     *             @OA\Property(property="lat", type="float", example="55.74039"),
     *             @OA\Property(property="lng", type="float", example="37.60216"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Деятельность создана", @OA\JsonContent())
     * )
     */
    public function store(BuildingRequest $request)
    {
        $validated = $request->validated();

        $building = Building::create($validated);
        return new BuildingResource($building);
    }

    /**
     * @OA\Put(
     *     path="/buildings/{id}",
     *     tags={"Buildings"},
     *     summary="Обновить данные о здании",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"address", "lat", "lng"},
     *              @OA\Property(property="address", type="string", example="1-й Зачатьевский пер., д.6ст1"),
     *              @OA\Property(property="lat", type="float", example="55.74039"),
     *              @OA\Property(property="lng", type="float", example="37.60216"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Здание обновлено", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Здание не найдено")
     * )
     */
    public function update(BuildingRequest $request)
    {
        $validated = $request->validated();

        try {
            $building = Building::findOrFail($request->id ?? null);
        } catch (\Exception $e) {
            // model not found
            return response()->json(['message' => 'Здание не найдено'], 404);
        }

        $building->update($validated);

        return new BuildingResource($building);
    }

    /**
     * @OA\Delete(
     *     path="/buildings/{id}",
     *     tags={"Buildings"},
     *     summary="Удалить здание",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Здание удалено", @OA\JsonContent())
     * )
     */
    public function destroy(Request $request)
    {
        Building::find($request->id ?? null)?->delete();
        return response()->json(['message' => 'Удалено']);
    }
}
