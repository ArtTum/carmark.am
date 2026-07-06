<?php

declare(strict_types=1);

require __DIR__ . '/app/app.php';
require __DIR__ . '/app/render.php';

boot_app();
handle_post_actions();

$data = read_data();
$route = route_path();

if ($route === 'admin/login') {
    echo render_admin_login();
    exit;
}

if ($route === 'admin') {
    require_admin();
    echo render_layout('Dashboard', render_admin_dashboard($data), ['admin' => true]);
    exit;
}

if ($route === 'admin/cars') {
    require_admin();
    echo render_layout('Vehicles', render_admin_cars($data), ['admin' => true]);
    exit;
}

if ($route === 'admin/leads') {
    require_admin();
    echo render_layout('Leads', render_admin_leads($data), ['admin' => true]);
    exit;
}

if ($route === 'admin/content') {
    require_admin();
    echo render_layout('Content', render_admin_content($data), ['admin' => true]);
    exit;
}

if ($route === 'admin/settings') {
    require_admin();
    echo render_layout('Settings', render_admin_settings($data), ['admin' => true]);
    exit;
}

if ($route === '' || $route === 'home') {
    echo render_layout(t('hero.title'), render_home($data));
    exit;
}

if ($route === 'inventory' || $route === 'products') {
    echo render_layout(t('inventory.title'), render_inventory($data));
    exit;
}

if (preg_match('#^cars/([a-z0-9-]+)$#', $route, $matches)) {
    $car = car_by_slug($data, $matches[1]);
    if ($car) {
        echo render_layout(localized($car['title']), render_product($data, $car));
        exit;
    }
}

if ($route === 'auctions') {
    $content = '<section class="page-hero compact"><div class="container"><h1>' . h(t('nav.auctions')) . '</h1><p>' . h(t('home.services_copy')) . '</p></div></section>';
    $content .= render_auction_section($data['auctions']);
    echo render_layout(t('nav.auctions'), $content);
    exit;
}

if (in_array($route, ['about', 'services', 'how-to-buy'], true)) {
    echo render_layout(t('nav.' . ($route === 'how-to-buy' ? 'how' : $route)), render_content_page($data, $route));
    exit;
}

if ($route === 'contact') {
    echo render_layout(t('nav.contact'), render_contact($data));
    exit;
}

if ($route === 'calculator') {
    echo render_layout(t('page.calculator'), render_calculator());
    exit;
}

if ($route === 'login' || $route === 'register') {
    echo render_layout($route === 'register' ? 'Register' : 'Log in', render_auth($route));
    exit;
}

if (in_array($route, ['profile', 'personal-info', 'payment-method', 'change-password'], true)) {
    echo render_layout(t('profile.title'), render_profile($data, 'profile'));
    exit;
}

if ($route === 'watchlist') {
    echo render_layout(t('profile.watchlist'), render_profile($data, 'watchlist'));
    exit;
}

if ($route === 'my-bids') {
    echo render_layout(t('profile.bids'), render_profile($data, 'my-bids'));
    exit;
}

if ($route === 'saved-searches') {
    $content = '<section class="container profile-page"><aside class="profile-side"><h1>' . h(t('profile.title')) . '</h1><a href="' . h(url('profile')) . '">Personal info</a><a href="' . h(url('watchlist')) . '">' . h(t('profile.watchlist')) . '</a><a href="' . h(url('my-bids')) . '">' . h(t('profile.bids')) . '</a></aside><div class="profile-main"><h2>Saved searches</h2><div class="chips"><a class="chip" href="' . h(url('inventory', ['make' => 'Toyota'])) . '">Toyota auctions</a><a class="chip" href="' . h(url('inventory', ['body' => 'Sedan'])) . '">Sedan under $30k</a><a class="chip" href="' . h(url('inventory', ['auction' => 'IAAI'])) . '">IAAI live lots</a></div></div></section>';
    echo render_layout('Saved searches', $content);
    exit;
}

http_response_code(404);
echo render_layout('Page not found', '<section class="page-hero compact"><div class="container"><h1>404</h1><p>Page not found.</p></div></section>');
