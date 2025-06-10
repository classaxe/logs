<?php
namespace App\Http\Controllers;

use App\Models\Park;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ParksController extends Controller
{
    public static function parkGrids(string $lat0, $lng0, $lat1, $lng1): JsonResponse
    {
        $parks = Park::getParks($lat0, $lng0, $lat1, $lng1);
        $data = [];
        foreach ($parks as $park) {
            $data[] = [
                'type' => 'Feature',
                'properties' => [
                    'program' => $park->program,
                    'pota' => $park->pota,
                    'wwff' => $park->wwff,
                    'name' => $park->name
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float)$park->lng,
                        (float)$park->lat
                    ]
                ]
            ];
        }
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $data
        ], 200);
    }

    public static function park(string $id): JsonResponse
    {
        $park = Park::where('reference', $id)->first();
        if (!$park) {
            return response()->json([
                'type' => 'Park',
            ], 404);
        }
        return response()->json([
            'name' => $park->name,
            'program' => $park->program,
            'latitude' => $park->lat,
            'longitude' => $park->lng,
        ], 200);
    }
}
