<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class EndpointController extends Controller
{

    public function fetchAndTransformData(): \Illuminate\Http\JsonResponse{
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get('https://api.sokanacademy.com/api/announcements/blog-index-header');

            if ($response->successful()) {
                $data = $response->json();

                $formattedData = collect();

                collect($data['data'])->each(function ($item) use ($formattedData) {
                    $categoryName = $item['all']['category_name'];
                    $title = $item['all']['title'];
                    $viewsCount = $item['all']['views_count'];

                    $formattedData->push([
                        'category_name' => $categoryName,
                        'title' => $title,
                        'views_count' => $viewsCount,
                    ]);
                });

                $result = $formattedData->groupBy('category_name')->map(function ($group) {
                    return $group->map(function ($item) {
                        return [
                            'title' => $item['title'],
                            'views_count' => $item['views_count'],
                        ];
                    });
                });

                return response()->json($result);
            }

            return response()->json(['error' => 'Unable to fetch data'], $response->status());

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
