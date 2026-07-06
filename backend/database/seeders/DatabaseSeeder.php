<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@carmark.am',
        ], [
            'name' => 'CarMark Admin',
            'password' => Hash::make('admin123'),
        ]);

        foreach ($this->settings() as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        foreach ($this->pages() as $page) {
            Page::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }

        foreach ($this->auctions() as $auction) {
            Auction::query()->updateOrCreate(['name' => $auction['name']], $auction);
        }

        foreach ($this->vehicles() as $vehicleData) {
            $images = $vehicleData['images'];
            unset($vehicleData['images']);

            $vehicle = Vehicle::query()->updateOrCreate(['slug' => $vehicleData['slug']], $vehicleData);
            $vehicle->images()->delete();

            foreach ($images as $index => $path) {
                $vehicle->images()->create(['path' => $path, 'sort_order' => $index]);
            }
        }
    }

    private function settings(): array
    {
        return [
            'site_name' => 'CarMark',
            'email' => 'info@carmark.am',
            'phone' => '+374 968301',
            'address' => [
                'hy' => 'Երևան, Խանջյան 41',
                'en' => '41 Khanjyan St, Yerevan',
                'ru' => 'Ереван, ул. Ханджяна 41',
            ],
            'languages' => ['hy', 'en', 'ru'],
        ];
    }

    private function pages(): array
    {
        return [
            [
                'slug' => 'about',
                'title' => [
                    'hy' => 'Մեր մասին',
                    'en' => 'About CarMark',
                    'ru' => 'О CarMark',
                ],
                'body' => [
                    'hy' => 'CarMark-ը ավտոաճուրդներից մեքենաների ընտրության, ստուգման, գնման և տեղափոխման հարթակ է։',
                    'en' => 'CarMark helps buyers source, inspect, bid, buy and ship cars from major auto auctions.',
                    'ru' => 'CarMark помогает подбирать, проверять, покупать и доставлять автомобили с крупных автоаукционов.',
                ],
            ],
            [
                'slug' => 'services',
                'title' => [
                    'hy' => 'Ծառայություններ',
                    'en' => 'Services',
                    'ru' => 'Услуги',
                ],
                'body' => [
                    'hy' => 'Աճուրդային հաշիվ, մեքենայի պատմության ստուգում, բիդինգ, վճարման ուղեկցում և լոգիստիկա։',
                    'en' => 'Auction access, history checks, bidding support, payment guidance and logistics.',
                    'ru' => 'Доступ к аукционам, проверка истории, сопровождение ставок, оплаты и логистики.',
                ],
            ],
            [
                'slug' => 'how-to-buy',
                'title' => [
                    'hy' => 'Ինչպես գնել',
                    'en' => 'How to buy',
                    'ru' => 'Как купить',
                ],
                'body' => [
                    'hy' => 'Ընտրեք մեքենան, թողեք հայտ, հաստատեք հաշվարկը, մասնակցեք աճուրդին և հետևեք առաքմանը։',
                    'en' => 'Choose a car, send a request, approve the estimate, bid and track shipping milestones.',
                    'ru' => 'Выберите авто, отправьте заявку, подтвердите расчет, участвуйте в торгах и отслеживайте доставку.',
                ],
            ],
        ];
    }

    private function auctions(): array
    {
        return [
            [
                'name' => 'Ashland (KY)',
                'address' => '123 Four Wheel Dr, Ashland, KY 41102',
                'country' => 'USA',
                'starts_at' => '2026-07-12 09:00:00',
                'lots_count' => 556,
                'buyer_fee' => 275,
                'image' => '/assets/extracted/12-763c3cf21bed.png',
                'active' => true,
            ],
            [
                'name' => 'Public car auction in Los Angeles - CA',
                'address' => '18300 S. Vermont Ave, Gardena, CA 90248',
                'country' => 'USA',
                'starts_at' => '2026-07-13 11:30:00',
                'lots_count' => 556,
                'buyer_fee' => 275,
                'image' => '/assets/extracted/11-8908995e8e02.png',
                'active' => true,
            ],
            [
                'name' => 'Atlanta North (GA)',
                'address' => '6242 Blackacre Trl. N.W, Acworth, GA 30101',
                'country' => 'USA',
                'starts_at' => '2026-07-14 10:00:00',
                'lots_count' => 556,
                'buyer_fee' => 310,
                'image' => '/assets/extracted/01-bd0f687c8cf3.png',
                'active' => true,
            ],
        ];
    }

    private function vehicles(): array
    {
        return [
            [
                'slug' => 'toyota-sequoia-2001',
                'title' => [
                    'hy' => '2001 Toyota Sequoia 4.7L վաճառք Վալդորֆում',
                    'en' => '2001 Toyota Sequoia 4.7L for sale in Waldorf',
                    'ru' => '2001 Toyota Sequoia 4.7L на продажу в Waldorf',
                ],
                'lot' => '37717811',
                'vin' => 'LVVV6488498M555',
                'year' => 2001,
                'make' => 'Toyota',
                'model' => 'Sequoia',
                'body' => 'SUV',
                'engine' => '4.7 L 8 Cylinders',
                'transmission' => 'Automatic',
                'fuel' => 'Gasoline',
                'color' => 'White',
                'condition' => 'Run & Drive',
                'location' => 'Waldorf - MD',
                'auction' => 'IAAI',
                'sale_date' => '2026-07-15 07:00:00',
                'odometer' => '117,630 mi',
                'current_bid' => 5025,
                'buy_now' => 25500,
                'shipping_fee' => 275,
                'featured' => true,
                'private_sale' => false,
                'status' => 'published',
                'images' => ['/assets/extracted/03-17064740bf1e.jpg', '/assets/extracted/14-28b124d87f27.png', '/assets/extracted/18-a93576670a23.jpg'],
            ],
            [
                'slug' => 'dodge-charger-gt-2022',
                'title' => ['hy' => '2022 Dodge Charger GT', 'en' => '2022 Dodge Charger GT', 'ru' => '2022 Dodge Charger GT'],
                'lot' => '12555884564',
                'vin' => '2C3CDXHG1NH145213',
                'year' => 2022,
                'make' => 'Dodge',
                'model' => 'Charger GT',
                'body' => 'Sedan',
                'engine' => '3.6 L',
                'transmission' => 'Automatic',
                'fuel' => 'Gasoline',
                'color' => 'Red',
                'condition' => 'Front Damage',
                'location' => 'Los Angeles - CA',
                'auction' => 'Copart',
                'sale_date' => '2026-07-16 09:30:00',
                'odometer' => '30,226 mi',
                'current_bid' => 8700,
                'buy_now' => 28500,
                'shipping_fee' => 275,
                'featured' => true,
                'private_sale' => false,
                'status' => 'published',
                'images' => ['/assets/extracted/04-214f9796b0b8.jpg', '/assets/extracted/18-a93576670a23.jpg'],
            ],
            [
                'slug' => 'chevrolet-bolt-ev-2022',
                'title' => ['hy' => '2022 Chevrolet Bolt EV FWD 2LT', 'en' => '2022 Chevrolet Bolt EV FWD 2LT', 'ru' => '2022 Chevrolet Bolt EV FWD 2LT'],
                'lot' => '38303409',
                'vin' => '1G1FX6S07N4110834',
                'year' => 2022,
                'make' => 'Chevrolet',
                'model' => 'Bolt EV',
                'body' => 'Hatchback',
                'engine' => 'Electric',
                'transmission' => 'Automatic',
                'fuel' => 'Electric',
                'color' => 'Gray',
                'condition' => 'Damaged',
                'location' => 'Los Angeles - CA',
                'auction' => 'IAAI',
                'sale_date' => '2026-07-17 11:00:00',
                'odometer' => '15,900 mi',
                'current_bid' => 5025,
                'buy_now' => 22000,
                'shipping_fee' => 275,
                'featured' => true,
                'private_sale' => false,
                'status' => 'published',
                'images' => ['/assets/extracted/15-b5c50c63b3b9.jpg', '/assets/extracted/16-dcd4befb4127.png'],
            ],
            [
                'slug' => 'hyundai-sonata-sel-2023',
                'title' => ['hy' => '2023 Hyundai Sonata SEL', 'en' => '2023 Hyundai Sonata SEL', 'ru' => '2023 Hyundai Sonata SEL'],
                'lot' => '38317147',
                'vin' => 'KMHL64JA2PA312102',
                'year' => 2023,
                'make' => 'Hyundai',
                'model' => 'Sonata SEL',
                'body' => 'Sedan',
                'engine' => '2.5 L',
                'transmission' => 'Automatic',
                'fuel' => 'Gasoline',
                'color' => 'Silver',
                'condition' => 'Run & Drive',
                'location' => 'Atlanta North - GA',
                'auction' => 'IAAI',
                'sale_date' => '2026-07-18 10:00:00',
                'odometer' => '11,900 mi',
                'current_bid' => 25000,
                'buy_now' => 32000,
                'shipping_fee' => 325,
                'featured' => false,
                'private_sale' => true,
                'status' => 'published',
                'images' => ['/assets/extracted/13-bbf2df81ce8e.png'],
            ],
            [
                'slug' => 'ram-2500-tradesman-2018',
                'title' => ['hy' => '2018 Ram 2500 Tradesman 4x4', 'en' => '2018 Ram 2500 Tradesman 4x4', 'ru' => '2018 Ram 2500 Tradesman 4x4'],
                'lot' => '38318729',
                'vin' => '3C6UR5CJ7JG267233',
                'year' => 2018,
                'make' => 'Ram',
                'model' => '2500 Tradesman',
                'body' => 'Pickup',
                'engine' => '6.4 L',
                'transmission' => 'Automatic',
                'fuel' => 'Gasoline',
                'color' => 'White',
                'condition' => 'Normal Wear',
                'location' => 'Ashland - KY',
                'auction' => 'Copart',
                'sale_date' => '2026-07-19 08:00:00',
                'odometer' => '119,900 mi',
                'current_bid' => 25000,
                'buy_now' => 34800,
                'shipping_fee' => 360,
                'featured' => false,
                'private_sale' => true,
                'status' => 'published',
                'images' => ['/assets/extracted/14-28b124d87f27.png'],
            ],
            [
                'slug' => 'chevrolet-malibu-lt-2018',
                'title' => ['hy' => '2018 Chevrolet Malibu LT', 'en' => '2018 Chevrolet Malibu LT', 'ru' => '2018 Chevrolet Malibu LT'],
                'lot' => '38251373',
                'vin' => '1G1ZD5ST8JF273415',
                'year' => 2018,
                'make' => 'Chevrolet',
                'model' => 'Malibu LT',
                'body' => 'Sedan',
                'engine' => '1.5 L',
                'transmission' => 'Automatic',
                'fuel' => 'Gasoline',
                'color' => 'Red',
                'condition' => 'Rear Damage',
                'location' => 'Atlanta North - GA',
                'auction' => 'IAAI',
                'sale_date' => '2026-07-20 12:00:00',
                'odometer' => '86,000 mi',
                'current_bid' => 5025,
                'buy_now' => 20500,
                'shipping_fee' => 310,
                'featured' => true,
                'private_sale' => false,
                'status' => 'published',
                'images' => ['/assets/extracted/16-dcd4befb4127.png'],
            ],
        ];
    }
}
