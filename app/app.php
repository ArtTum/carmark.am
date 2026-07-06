<?php

declare(strict_types=1);

const APP_LANGS = ['hy', 'en', 'ru'];

function boot_app(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!is_dir(storage_dir())) {
        mkdir(storage_dir(), 0775, true);
    }

    if (!is_file(data_file())) {
        write_data(require __DIR__ . '/seed.php');
    }

    detect_language();
}

function storage_dir(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage';
}

function data_file(): string
{
    return storage_dir() . DIRECTORY_SEPARATOR . 'data.json';
}

function read_data(): array
{
    $json = file_get_contents(data_file());
    $data = json_decode($json ?: '', true);

    if (!is_array($data)) {
        $data = require __DIR__ . '/seed.php';
        write_data($data);
    }

    return $data;
}

function write_data(array $data): void
{
    $tmp = data_file() . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    rename($tmp, data_file());
}

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_path(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = trim($dir, '/');
    return $dir === '' ? '' : '/' . $dir;
}

function current_path(): string
{
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
    $base = trim(base_path(), '/');

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = trim(substr($path, strlen($base)), '/');
    }

    return $path;
}

function detect_language(): void
{
    $path = current_path();
    $first = explode('/', $path)[0] ?? '';

    if (isset($_GET['lang']) && in_array($_GET['lang'], APP_LANGS, true)) {
        $_SESSION['lang'] = $_GET['lang'];
        return;
    }

    if (in_array($first, APP_LANGS, true)) {
        $_SESSION['lang'] = $first;
        return;
    }

    $_SESSION['lang'] = $_SESSION['lang'] ?? 'hy';
}

function lang(): string
{
    return $_SESSION['lang'] ?? 'hy';
}

function route_path(): string
{
    $path = current_path();
    $parts = $path === '' ? [] : explode('/', $path);

    if (($parts[0] ?? '') !== '' && in_array($parts[0], APP_LANGS, true)) {
        array_shift($parts);
    }

    return trim(implode('/', $parts), '/');
}

function url(string $route = '', array $params = []): string
{
    $route = trim($route, '/');
    $base = base_path();
    $isAdmin = $route === 'admin' || str_starts_with($route, 'admin/');
    $path = $isAdmin ? $route : trim(lang() . '/' . $route, '/');
    $query = $params ? '?' . http_build_query($params) : '';

    return $base . '/' . $path . $query;
}

function asset_url(string $path): string
{
    if (preg_match('/^https?:\/\//', $path)) {
        return $path;
    }

    return base_path() . '/' . ltrim($path, '/');
}

function translations(): array
{
    return [
        'nav.buy' => ['hy' => 'Գնել մեքենա', 'en' => 'Buy a car', 'ru' => 'Купить авто'],
        'nav.auctions' => ['hy' => 'Աճուրդներ', 'en' => 'Auctions', 'ru' => 'Аукционы'],
        'nav.categories' => ['hy' => 'Ընտրություն', 'en' => 'Categories', 'ru' => 'Категории'],
        'nav.private' => ['hy' => 'Առկա տեսականի', 'en' => 'Private sales', 'ru' => 'Частные авто'],
        'nav.about' => ['hy' => 'Մեր մասին', 'en' => 'About', 'ru' => 'О нас'],
        'nav.services' => ['hy' => 'Ծառայություններ', 'en' => 'Services', 'ru' => 'Услуги'],
        'nav.how' => ['hy' => 'Ինչպես գնել', 'en' => 'How to buy', 'ru' => 'Как купить'],
        'nav.contact' => ['hy' => 'Կապ մեզ հետ', 'en' => 'Contact', 'ru' => 'Контакты'],
        'nav.login' => ['hy' => 'Հաշիվ', 'en' => 'Account', 'ru' => 'Кабинет'],
        'nav.sell' => ['hy' => 'Մուտք գործել', 'en' => 'Sign in', 'ru' => 'Войти'],
        'search.placeholder' => ['hy' => 'Որոնել մեքենաներ ըստ մակնիշի, մոդելի ...', 'en' => 'Search by make, model or lot ...', 'ru' => 'Поиск по марке, модели или лоту ...'],
        'hero.title' => ['hy' => 'Ապահովագրական ավտո աճուրդ', 'en' => 'Insurance auto auctions', 'ru' => 'Страховые автоаукционы'],
        'hero.copy' => ['hy' => 'Ապահովի համար լավագույն գները՝ առանց ժամանակ կորցնելու, բաց և հարմար workflow-ով։', 'en' => 'Find auction cars, calculate the full landed cost and manage the purchase from one place.', 'ru' => 'Подбирайте авто с аукционов, считайте полную стоимость и ведите покупку в одном месте.'],
        'hero.cta' => ['hy' => 'Որոնել', 'en' => 'Search', 'ru' => 'Искать'],
        'field.make' => ['hy' => 'Մակնիշ', 'en' => 'Make', 'ru' => 'Марка'],
        'field.model' => ['hy' => 'Մոդել', 'en' => 'Model', 'ru' => 'Модель'],
        'field.year_from' => ['hy' => 'Սկսած', 'en' => 'From', 'ru' => 'От'],
        'field.year_to' => ['hy' => 'Մինչև', 'en' => 'To', 'ru' => 'До'],
        'field.any' => ['hy' => 'Բոլոր տարբերակները', 'en' => 'Any option', 'ru' => 'Любой вариант'],
        'home.featured' => ['hy' => 'Ամենապահանջված մեքենաները', 'en' => 'Featured vehicles', 'ru' => 'Популярные автомобили'],
        'home.auctions' => ['hy' => 'Մոտակա և ընթացիկ աճուրդները', 'en' => 'Upcoming auctions', 'ru' => 'Ближайшие аукционы'],
        'home.private' => ['hy' => 'Առկա տեսականի', 'en' => 'Private inventory', 'ru' => 'Частные предложения'],
        'home.brands' => ['hy' => 'Մեքենաների վաճառք ըստ մակնիշի', 'en' => 'Shop by brand', 'ru' => 'Поиск по бренду'],
        'home.services_title' => ['hy' => 'Բոլոր ծառայությունները մեկ վայրում', 'en' => 'Every service in one place', 'ru' => 'Все услуги в одном месте'],
        'home.services_copy' => ['hy' => 'IAA և Copart աճուրդներից մինչև վճարում, բեռնափոխադրում ու փաստաթղթեր։', 'en' => 'From IAAI and Copart sourcing to payment, logistics and paperwork.', 'ru' => 'От подбора на IAAI и Copart до оплаты, логистики и документов.'],
        'home.more' => ['hy' => 'Տեսնել բոլորը', 'en' => 'View all', 'ru' => 'Смотреть все'],
        'inventory.title' => ['hy' => 'Առաջարկվող ֆիլտրեր', 'en' => 'Inventory', 'ru' => 'Каталог'],
        'inventory.filters' => ['hy' => 'Ֆիլտրեր', 'en' => 'Filters', 'ru' => 'Фильтры'],
        'inventory.results' => ['hy' => 'գտնված մեքենաներ', 'en' => 'vehicles found', 'ru' => 'автомобилей найдено'],
        'inventory.sort' => ['hy' => 'Դասավորել ըստ', 'en' => 'Sort by', 'ru' => 'Сортировать'],
        'sort.newest' => ['hy' => 'Նորերը սկզբում', 'en' => 'Newest first', 'ru' => 'Сначала новые'],
        'sort.price' => ['hy' => 'Գին՝ աճման', 'en' => 'Price ascending', 'ru' => 'Цена по возрастанию'],
        'btn.watch' => ['hy' => 'Պահպանել', 'en' => 'Watch', 'ru' => 'Сохранить'],
        'btn.unwatch' => ['hy' => 'Պահպանված', 'en' => 'Saved', 'ru' => 'Сохранено'],
        'btn.bid' => ['hy' => 'Գին առաջարկել', 'en' => 'Place bid', 'ru' => 'Сделать ставку'],
        'btn.buy_now' => ['hy' => 'Գնել հիմա', 'en' => 'Buy now', 'ru' => 'Купить сейчас'],
        'btn.details' => ['hy' => 'Մանրամասն', 'en' => 'Details', 'ru' => 'Подробнее'],
        'btn.calculate' => ['hy' => 'Հաշվել', 'en' => 'Calculate', 'ru' => 'Рассчитать'],
        'product.auction_info' => ['hy' => 'Աճուրդային տեղեկատվություն', 'en' => 'Auction information', 'ru' => 'Информация аукциона'],
        'product.specs' => ['hy' => 'Մեքենայի տվյալներ', 'en' => 'Vehicle details', 'ru' => 'Характеристики'],
        'product.ask' => ['hy' => 'Օգնություն պետք է այս մեքենայի համար', 'en' => 'Need help with this vehicle?', 'ru' => 'Нужна помощь с этим авто?'],
        'product.related' => ['hy' => 'Մեքենաներ, որոնք ձեզ կարող են դուր գալ', 'en' => 'Vehicles you may like', 'ru' => 'Похожие автомобили'],
        'form.name' => ['hy' => 'Անուն', 'en' => 'Name', 'ru' => 'Имя'],
        'form.email' => ['hy' => 'Էլ. հասցե', 'en' => 'Email', 'ru' => 'Email'],
        'form.phone' => ['hy' => 'Հեռախոս', 'en' => 'Phone', 'ru' => 'Телефон'],
        'form.message' => ['hy' => 'Հաղորդագրություն', 'en' => 'Message', 'ru' => 'Сообщение'],
        'form.send' => ['hy' => 'Ուղարկել', 'en' => 'Send', 'ru' => 'Отправить'],
        'page.calculator' => ['hy' => 'Արժեքի հաշվիչ', 'en' => 'Cost calculator', 'ru' => 'Калькулятор стоимости'],
        'profile.title' => ['hy' => 'Իմ հաշիվը', 'en' => 'My account', 'ru' => 'Мой кабинет'],
        'profile.watchlist' => ['hy' => 'Պահպանվածներ', 'en' => 'Watchlist', 'ru' => 'Избранное'],
        'profile.bids' => ['hy' => 'Իմ առաջարկները', 'en' => 'My bids', 'ru' => 'Мои ставки'],
        'flash.saved' => ['hy' => 'Տվյալները պահպանվեցին։', 'en' => 'Saved successfully.', 'ru' => 'Данные сохранены.'],
        'flash.sent' => ['hy' => 'Հայտը ուղարկվեց։', 'en' => 'Request sent.', 'ru' => 'Заявка отправлена.'],
        'footer.rights' => ['hy' => 'Բոլոր իրավունքները պաշտպանված են', 'en' => 'All rights reserved', 'ru' => 'Все права защищены'],
    ];
}

function t(string $key, array $replace = []): string
{
    $value = translations()[$key] ?? ['en' => $key];
    $text = localized($value);

    foreach ($replace as $name => $replacement) {
        $text = str_replace(':' . $name, (string) $replacement, $text);
    }

    return $text;
}

function localized(mixed $value): string
{
    if (is_array($value)) {
        return (string) ($value[lang()] ?? $value['en'] ?? reset($value) ?? '');
    }

    return (string) $value;
}

function money(mixed $value): string
{
    return '$' . number_format((float) $value, 0);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Session expired.');
    }
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function redirect_to(string $route = '', array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

function redirect_raw(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function post_string(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function post_float(string $key, float $default = 0): float
{
    return (float) str_replace(',', '', (string) ($_POST[$key] ?? $default));
}

function is_admin(): bool
{
    return ($_SESSION['admin'] ?? false) === true;
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect_raw(base_path() . '/admin/login');
    }
}

function max_id(array $items): int
{
    $ids = array_map(static fn ($item) => (int) ($item['id'] ?? 0), $items);
    return $ids ? max($ids) : 0;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text) ?: 'item';
    return trim($text, '-') ?: 'item';
}

function car_by_slug(array $data, string $slug): ?array
{
    foreach ($data['cars'] as $car) {
        if (($car['slug'] ?? '') === $slug && ($car['status'] ?? 'published') === 'published') {
            return $car;
        }
    }

    return null;
}

function car_by_id(array $data, int $id): ?array
{
    foreach ($data['cars'] as $car) {
        if ((int) $car['id'] === $id) {
            return $car;
        }
    }

    return null;
}

function available_values(array $cars, string $key): array
{
    $values = [];
    foreach ($cars as $car) {
        if (!empty($car[$key])) {
            $values[] = (string) $car[$key];
        }
    }
    $values = array_values(array_unique($values));
    sort($values);

    return $values;
}

function filtered_cars(array $data, array $query = []): array
{
    $cars = array_values(array_filter($data['cars'], static fn ($car) => ($car['status'] ?? 'published') === 'published'));

    foreach (['make', 'model', 'body', 'auction', 'fuel'] as $field) {
        if (!empty($query[$field])) {
            $needle = strtolower((string) $query[$field]);
            $cars = array_values(array_filter($cars, static fn ($car) => strtolower((string) ($car[$field] ?? '')) === $needle));
        }
    }

    if (!empty($query['q'])) {
        $needle = mb_strtolower((string) $query['q'], 'UTF-8');
        $cars = array_values(array_filter($cars, static function ($car) use ($needle) {
            $haystack = mb_strtolower(localized($car['title']) . ' ' . ($car['lot'] ?? '') . ' ' . ($car['vin'] ?? ''), 'UTF-8');
            return str_contains($haystack, $needle);
        }));
    }

    if (!empty($query['year_from'])) {
        $cars = array_values(array_filter($cars, static fn ($car) => (int) $car['year'] >= (int) $query['year_from']));
    }

    if (!empty($query['year_to'])) {
        $cars = array_values(array_filter($cars, static fn ($car) => (int) $car['year'] <= (int) $query['year_to']));
    }

    if (($query['sort'] ?? '') === 'price') {
        usort($cars, static fn ($a, $b) => (float) $a['current_bid'] <=> (float) $b['current_bid']);
    } else {
        usort($cars, static fn ($a, $b) => (int) $b['year'] <=> (int) $a['year']);
    }

    return $cars;
}

function watched_ids(): array
{
    return array_map('intval', $_SESSION['watchlist'] ?? []);
}

function is_watched(int $id): bool
{
    return in_array($id, watched_ids(), true);
}

function public_route_after_post(): string
{
    return $_POST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? url(''));
}

function handle_post_actions(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    verify_csrf();

    $action = post_string('action');
    $data = read_data();

    if ($action === 'admin_login') {
        $settings = $data['settings'];
        if (post_string('email') === $settings['admin_user'] && post_string('password') === $settings['admin_password']) {
            $_SESSION['admin'] = true;
            flash('Welcome back.');
            redirect_raw(base_path() . '/admin');
        }

        flash('Invalid admin credentials.', 'error');
        redirect_raw(base_path() . '/admin/login');
    }

    if ($action === 'admin_logout') {
        unset($_SESSION['admin']);
        redirect_raw(base_path() . '/admin/login');
    }

    if (str_starts_with($action, 'admin_')) {
        require_admin();
    }

    switch ($action) {
        case 'lead':
            $data['leads'][] = [
                'id' => max_id($data['leads']) + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'car_id' => (int) ($_POST['car_id'] ?? 0),
                'type' => post_string('type', 'request'),
                'name' => post_string('name'),
                'email' => post_string('email'),
                'phone' => post_string('phone'),
                'message' => post_string('message'),
                'status' => 'new',
            ];
            write_data($data);
            flash(t('flash.sent'));
            redirect_raw(public_route_after_post());

        case 'bid':
            $carId = (int) ($_POST['car_id'] ?? 0);
            $amount = post_float('amount');
            $data['bids'][] = [
                'id' => max_id($data['bids']) + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'car_id' => $carId,
                'amount' => $amount,
                'name' => post_string('name', 'Guest'),
                'email' => post_string('email'),
                'status' => 'pending',
            ];
            write_data($data);
            flash(t('flash.sent'));
            redirect_raw(public_route_after_post());

        case 'toggle_watch':
            $id = (int) ($_POST['car_id'] ?? 0);
            $watch = watched_ids();
            if (in_array($id, $watch, true)) {
                $watch = array_values(array_diff($watch, [$id]));
            } else {
                $watch[] = $id;
            }
            $_SESSION['watchlist'] = $watch;
            redirect_raw(public_route_after_post());

        case 'register':
            $data['users'][] = [
                'id' => max_id($data['users']) + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'name' => post_string('name'),
                'email' => post_string('email'),
            ];
            $_SESSION['customer'] = post_string('email');
            write_data($data);
            flash(t('flash.saved'));
            redirect_to('profile');

        case 'login':
            $_SESSION['customer'] = post_string('email');
            flash(t('flash.saved'));
            redirect_to('profile');

        case 'admin_save_car':
            save_admin_car($data);
            flash('Vehicle saved.');
            redirect_raw(base_path() . '/admin/cars');

        case 'admin_delete_car':
            $id = (int) ($_POST['id'] ?? 0);
            $data['cars'] = array_values(array_filter($data['cars'], static fn ($car) => (int) $car['id'] !== $id));
            write_data($data);
            flash('Vehicle deleted.');
            redirect_raw(base_path() . '/admin/cars');

        case 'admin_lead_status':
            $id = (int) ($_POST['id'] ?? 0);
            foreach ($data['leads'] as &$lead) {
                if ((int) $lead['id'] === $id) {
                    $lead['status'] = post_string('status', 'new');
                }
            }
            unset($lead);
            write_data($data);
            flash('Lead updated.');
            redirect_raw(base_path() . '/admin/leads');

        case 'admin_save_content':
            $key = post_string('key');
            if (isset($data['content'][$key])) {
                foreach (APP_LANGS as $code) {
                    $data['content'][$key]['title'][$code] = post_string('title_' . $code);
                    $data['content'][$key]['body'][$code] = post_string('body_' . $code);
                }
                write_data($data);
                flash('Content saved.');
            }
            redirect_raw(base_path() . '/admin/content');

        case 'admin_save_settings':
            $data['settings']['site_name'] = post_string('site_name', 'CarMark');
            $data['settings']['email'] = post_string('email');
            $data['settings']['phone'] = post_string('phone');
            $data['settings']['address']['hy'] = post_string('address_hy');
            $data['settings']['address']['en'] = post_string('address_en');
            $data['settings']['address']['ru'] = post_string('address_ru');
            write_data($data);
            flash('Settings saved.');
            redirect_raw(base_path() . '/admin/settings');
    }
}

function save_admin_car(array $data): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $images = array_values(array_filter(array_map('trim', explode(',', post_string('images')))));
    $payload = [
        'id' => $id > 0 ? $id : max_id($data['cars']) + 1,
        'slug' => post_string('slug') ?: slugify(post_string('title_en')),
        'title' => [
            'hy' => post_string('title_hy'),
            'en' => post_string('title_en'),
            'ru' => post_string('title_ru'),
        ],
        'lot' => post_string('lot'),
        'vin' => post_string('vin'),
        'year' => (int) post_float('year'),
        'make' => post_string('make'),
        'model' => post_string('model'),
        'body' => post_string('body'),
        'engine' => post_string('engine'),
        'transmission' => post_string('transmission'),
        'fuel' => post_string('fuel'),
        'color' => post_string('color'),
        'condition' => post_string('condition'),
        'location' => post_string('location'),
        'auction' => post_string('auction'),
        'sale_date' => post_string('sale_date'),
        'odometer' => post_string('odometer'),
        'current_bid' => post_float('current_bid'),
        'buy_now' => post_float('buy_now'),
        'shipping_fee' => post_float('shipping_fee'),
        'featured' => isset($_POST['featured']),
        'private_sale' => isset($_POST['private_sale']),
        'status' => post_string('status', 'published'),
        'images' => $images ?: ['assets/extracted/03-17064740bf1e.jpg'],
    ];

    $found = false;
    foreach ($data['cars'] as $index => $car) {
        if ((int) $car['id'] === $payload['id']) {
            $data['cars'][$index] = $payload;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $data['cars'][] = $payload;
    }

    write_data($data);
}

function admin_nav_items(): array
{
    return [
        'admin' => 'Dashboard',
        'admin/cars' => 'Vehicles',
        'admin/leads' => 'Leads',
        'admin/content' => 'Content',
        'admin/settings' => 'Settings',
    ];
}
