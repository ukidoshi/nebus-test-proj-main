<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Organization;

class OrganizationFilterService
{
    private array $validated_data;

    public function __construct(array $validated_data)
    {
        $this->validated_data = $validated_data;
    }

    public function getOrganizations() {
        // Параметры поиска
        $searchQuery = $this->validated_data['search'] ?? '';

        $filters = [];
        // Геофильтр
        $geoFilter = $this->getGeoFilter($this->validated_data);
        if ($geoFilter) {
            $filters[] = $geoFilter;
        }

        // Фильтр по зданию
        if (!empty($this->validated_data['building_id'])) {
            $filters[] = "building_id = {$this->validated_data['building_id']}";
        }

        // Фильтр по деятельности
        if (!empty($this->validated_data['activity_id'])) {
            $activity = Activity::find($this->validated_data['activity_id']);
            $ids = implode(', ', $activity->getAllChildrenIds());
            $filters[] = "activity_ids IN [$ids]";
        }

        // Создаем поисковый запрос
        $search = Organization::search($searchQuery, function ($meilisearch, string $query, array $options) use ($filters) {
            if ($filters) {
                $options['filter'] = $filters;
            }
            return $meilisearch->search($query, $options);
        });

        // Выполняем поиск с пагинацией
        $results = $search->paginate(20);

        // Получаем полные модели с отношениями
        $organizationIds = $results->pluck('id');
        $organizations = Organization::whereIn('id', $organizationIds)
            ->get()
            ->keyBy('id');

        // Сортируем результаты в том же порядке, что и в поиске
        $sortedOrganizations = $results->map(function ($result) use ($organizations) {
            return $organizations->get($result->id);
        })->filter();

        return $results->setCollection($sortedOrganizations);
    }

    /**
     * Получить фильтры для геопоиска
     */
    private function getGeoFilter(array $validated): string
    {
        if (empty($validated['geo_type'])) {
            return "";
        }

        return match ($validated['geo_type']) {
            'radius' => $this->buildRadiusFilter($validated),
            'box' => $this->buildBoxFilter($validated),
            default => ""
        };
    }

    private function buildRadiusFilter(array $validated)
    {
        $lat = $validated['lat'] ?? null;
        $lng = $validated['lng'] ?? null;

        if (!$lat || !$lng) {
            return "";
        }

        $radius = $validated['radius'] ?? 1000; // по умолчанию 1км

        return "_geoRadius({$lat}, {$lng}, {$radius})";
    }

    private function buildBoxFilter(array $validated)
    {
        $lat = $validated['lat'] ?? null;
        $lng = $validated['lng'] ?? null;
        $lat2 = $validated['lat_2'] ?? null;
        $lng2 = $validated['lng_2'] ?? null;

        if (!$lat || !$lng || !$lat2 || !$lng2) {
            return "";
        }

        // Определяем границы прямоугольника(нормализуем)
        $topLat = max($lat, $lat2);
        $bottomLat = min($lat, $lat2);
        $leftLng = max($lng, $lng2);
        $rightLng = min($lng, $lng2);

        return "_geoBoundingBox([$topLat, $leftLng], [$bottomLat, $rightLng])";
    }
}
