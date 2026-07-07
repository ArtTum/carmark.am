<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function site(): JsonResponse
    {
        return response()->json([
            'settings' => Setting::query()->pluck('value', 'key'),
            'pages' => Page::query()->get(),
        ]);
    }

    public function vehicles(Request $request): JsonResponse
    {
        $query = Vehicle::query()
            ->with('images')
            ->where('status', 'published');

        foreach (['make', 'model', 'body', 'auction', 'fuel'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->string($field));
            }
        }

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        if ($request->boolean('private_sale')) {
            $query->where('private_sale', true);
        }

        if ($request->boolean('buy_now')) {
            $query->whereNotNull('buy_now')->where('buy_now', '>', 0);
        }

        if ($request->filled('year_from')) {
            $query->where('year', '>=', (int) $request->input('year_from'));
        }

        if ($request->filled('year_to')) {
            $query->where('year', '<=', (int) $request->input('year_to'));
        }

        if ($request->filled('q')) {
            $needle = '%' . $request->string('q') . '%';
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery->where('make', 'like', $needle)
                    ->orWhere('model', 'like', $needle)
                    ->orWhere('lot', 'like', $needle)
                    ->orWhere('vin', 'like', $needle);
            });
        }

        if ((string) $request->string('sort') === 'price') {
            $query->orderBy('current_bid');
        } else {
            $query->orderByDesc('year')->orderByDesc('id');
        }

        return response()->json([
            'data' => $query->get()->map(fn (Vehicle $vehicle) => $this->vehiclePayload($vehicle)),
            'filters' => [
                'makes' => Vehicle::query()->where('status', 'published')->distinct()->orderBy('make')->pluck('make'),
                'models' => Vehicle::query()->where('status', 'published')->distinct()->orderBy('model')->pluck('model'),
                'bodies' => Vehicle::query()->where('status', 'published')->distinct()->orderBy('body')->pluck('body'),
                'auctions' => Vehicle::query()->where('status', 'published')->distinct()->orderBy('auction')->pluck('auction'),
                'fuels' => Vehicle::query()->where('status', 'published')->distinct()->orderBy('fuel')->pluck('fuel'),
            ],
        ]);
    }

    public function vehicle(string $slug): JsonResponse
    {
        $vehicle = Vehicle::query()
            ->with('images')
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Vehicle::query()
            ->with('images')
            ->where('status', 'published')
            ->where('id', '!=', $vehicle->id)
            ->limit(3)
            ->get()
            ->map(fn (Vehicle $item) => $this->vehiclePayload($item));

        return response()->json([
            'data' => $this->vehiclePayload($vehicle),
            'related' => $related,
        ]);
    }

    public function auctions(): JsonResponse
    {
        return response()->json([
            'data' => Auction::query()->where('active', true)->orderBy('id')->get(),
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        return response()->json(['data' => Page::query()->where('slug', $slug)->firstOrFail()]);
    }

    public function lead(Request $request): JsonResponse
    {
        $lead = Lead::query()->create($request->validate([
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'type' => ['nullable', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]));

        return response()->json(['message' => 'Lead created.', 'data' => $lead], 201);
    }

    public function bid(Request $request): JsonResponse
    {
        $bid = Bid::query()->create($request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'amount' => ['required', 'integer', 'min:100'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
        ]));

        return response()->json(['message' => 'Bid submitted.', 'data' => $bid], 201);
    }

    private function vehiclePayload(Vehicle $vehicle): array
    {
        return [
            ...$vehicle->toArray(),
            'images' => $vehicle->images->pluck('path')->values(),
        ];
    }
}
