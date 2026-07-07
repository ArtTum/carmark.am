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
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (
            $credentials['email'] !== env('ADMIN_EMAIL', 'admin@carmark.am') ||
            $credentials['password'] !== env('ADMIN_PASSWORD', 'admin123')
        ) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        return response()->json([
            'token' => env('ADMIN_API_TOKEN', 'local-admin-token'),
            'user' => ['name' => 'Admin', 'email' => $credentials['email']],
        ]);
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'vehicles' => Vehicle::query()->count(),
            'published' => Vehicle::query()->where('status', 'published')->count(),
            'leads' => Lead::query()->count(),
            'new_leads' => Lead::query()->where('status', 'new')->count(),
            'bids' => Bid::query()->count(),
            'bid_value' => Bid::query()->sum('amount'),
            'recent_leads' => Lead::query()->with('vehicle')->latest()->limit(6)->get(),
        ]);
    }

    public function vehicles(): JsonResponse
    {
        return response()->json([
            'data' => Vehicle::query()->with('images')->latest()->get()->map(fn (Vehicle $vehicle) => [
                ...$vehicle->toArray(),
                'images' => $vehicle->images->pluck('path')->values(),
            ]),
        ]);
    }

    public function storeVehicle(Request $request): JsonResponse
    {
        $vehicle = Vehicle::query()->create($this->vehicleData($request));
        $this->syncImages($vehicle, $request->input('images', []));

        return response()->json(['message' => 'Vehicle created.', 'data' => $vehicle->load('images')], 201);
    }

    public function updateVehicle(Request $request, Vehicle $vehicle): JsonResponse
    {
        $vehicle->update($this->vehicleData($request, $vehicle));
        $this->syncImages($vehicle, $request->input('images', []));

        return response()->json(['message' => 'Vehicle updated.', 'data' => $vehicle->load('images')]);
    }

    public function destroyVehicle(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted.']);
    }

    public function auctions(): JsonResponse
    {
        return response()->json(['data' => Auction::query()->latest()->get()]);
    }

    public function leads(): JsonResponse
    {
        return response()->json(['data' => Lead::query()->with('vehicle')->latest()->get()]);
    }

    public function updateLead(Request $request, Lead $lead): JsonResponse
    {
        $lead->update($request->validate([
            'status' => ['required', 'string', 'in:new,contacted,won,lost'],
        ]));

        return response()->json(['message' => 'Lead updated.', 'data' => $lead]);
    }

    public function pages(): JsonResponse
    {
        $this->ensureDefaultPages();

        return response()->json(['data' => Page::query()->orderBy('slug')->get()]);
    }

    public function updatePage(Request $request, Page $page): JsonResponse
    {
        $page->update($request->validate([
            'title' => ['required', 'array'],
            'body' => ['required', 'array'],
        ]));

        return response()->json(['message' => 'Page updated.', 'data' => $page]);
    }

    public function settings(): JsonResponse
    {
        return response()->json(['data' => Setting::query()->pluck('value', 'key')]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        foreach ($request->all() as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Settings updated.', 'data' => Setting::query()->pluck('value', 'key')]);
    }

    private function vehicleData(Request $request, ?Vehicle $vehicle = null): array
    {
        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:160'],
            'title' => ['required', 'array'],
            'lot' => ['nullable', 'string', 'max:80'],
            'vin' => ['nullable', 'string', 'max:80'],
            'year' => ['required', 'integer', 'min:1900', 'max:2035'],
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:80'],
            'engine' => ['nullable', 'string', 'max:80'],
            'transmission' => ['nullable', 'string', 'max:80'],
            'fuel' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:80'],
            'condition' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:160'],
            'auction' => ['nullable', 'string', 'max:80'],
            'sale_date' => ['nullable', 'date'],
            'odometer' => ['nullable', 'string', 'max:80'],
            'current_bid' => ['required', 'integer', 'min:0'],
            'buy_now' => ['nullable', 'integer', 'min:0'],
            'shipping_fee' => ['nullable', 'integer', 'min:0'],
            'featured' => ['boolean'],
            'private_sale' => ['boolean'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
        ]);

        $slug = $data['slug'] ?: Str::slug(($data['title']['en'] ?? $data['make'] . ' ' . $data['model']) . ' ' . $data['year']);
        $baseSlug = $slug;
        $counter = 2;

        while (Vehicle::query()->where('slug', $slug)->when($vehicle, fn ($query) => $query->where('id', '!=', $vehicle->id))->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $data['featured'] = (bool) ($data['featured'] ?? false);
        $data['private_sale'] = (bool) ($data['private_sale'] ?? false);

        return $data;
    }

    private function syncImages(Vehicle $vehicle, array $images): void
    {
        $vehicle->images()->delete();
        foreach (array_values(array_filter($images)) as $index => $path) {
            $vehicle->images()->create([
                'path' => $path,
                'sort_order' => $index,
            ]);
        }
    }

    private function ensureDefaultPages(): void
    {
        foreach ([
            ['slug' => 'home', 'title' => ['hy' => 'Գլխավոր', 'en' => 'Home', 'ru' => 'Главная'], 'body' => []],
            ['slug' => 'about', 'title' => ['hy' => 'Մեր մասին', 'en' => 'About us', 'ru' => 'О нас'], 'body' => []],
            ['slug' => 'services', 'title' => ['hy' => 'Ծառայություններ', 'en' => 'Services', 'ru' => 'Услуги'], 'body' => []],
            ['slug' => 'how-to-buy', 'title' => ['hy' => 'Ինչպես գնել', 'en' => 'How to buy', 'ru' => 'Как купить'], 'body' => []],
            ['slug' => 'contact', 'title' => ['hy' => 'Կապ մեզ հետ', 'en' => 'Contact', 'ru' => 'Контакты'], 'body' => []],
            ['slug' => 'faqs', 'title' => ['hy' => 'Հաճախ տրվող հարցեր', 'en' => 'FAQs', 'ru' => 'FAQ'], 'body' => []],
            ['slug' => 'calculator', 'title' => ['hy' => 'Հաշվիչ', 'en' => 'Calculator', 'ru' => 'Калькулятор'], 'body' => []],
        ] as $page) {
            Page::query()->firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
