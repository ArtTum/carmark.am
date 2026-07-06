<?php

declare(strict_types=1);

function render_layout(string $title, string $content, array $options = []): string
{
    $data = read_data();
    $isAdmin = (bool) ($options['admin'] ?? false);
    $bodyClass = $isAdmin ? 'admin-body' : 'site-body';

    ob_start();
    ?>
    <!doctype html>
    <html lang="<?= h(lang()) ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h($title) ?> | <?= h($data['settings']['site_name']) ?></title>
        <link rel="stylesheet" href="<?= h(asset_url('assets/css/styles.css')) ?>">
        <script defer src="<?= h(asset_url('assets/js/app.js')) ?>"></script>
    </head>
    <body class="<?= h($bodyClass) ?>">
        <?php if ($isAdmin): ?>
            <?= render_admin_shell($title, $content) ?>
        <?php else: ?>
            <?= render_public_header($data) ?>
            <main><?= $content ?></main>
            <?= render_public_footer($data) ?>
        <?php endif; ?>
        <?= render_flashes() ?>
    </body>
    </html>
    <?php
    return trim(ob_get_clean());
}

function render_flashes(): string
{
    $items = flashes();
    if (!$items) {
        return '';
    }

    ob_start();
    ?>
    <div class="toast-stack">
        <?php foreach ($items as $item): ?>
            <div class="toast <?= h($item['type']) ?>"><?= h($item['message']) ?></div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function render_logo(): string
{
    return '<a class="brand" href="' . h(url('')) . '" aria-label="CarMark home"><span class="brand-mark"></span><span>carmark</span></a>';
}

function render_public_header(array $data): string
{
    $nav = [
        'inventory' => t('nav.buy'),
        'auctions' => t('nav.auctions'),
        'services' => t('nav.services'),
        'how-to-buy' => t('nav.how'),
    ];

    ob_start();
    ?>
    <header class="site-header">
        <div class="header-main container">
            <?= render_logo() ?>
            <form class="header-search" action="<?= h(url('inventory')) ?>" method="get">
                <input type="search" name="q" value="<?= h($_GET['q'] ?? '') ?>" placeholder="<?= h(t('search.placeholder')) ?>">
                <button class="icon-search" type="submit" aria-label="<?= h(t('hero.cta')) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m21 21-4.4-4.4m1.4-5.1a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                </button>
            </form>
            <a class="quick-account" href="<?= h(url('profile')) ?>">
                <span class="quick-icon">□</span>
                <?= h(t('nav.login')) ?>
            </a>
            <button class="menu-toggle" type="button" data-menu-toggle aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="header-nav" data-mobile-menu>
            <div class="container nav-row">
                <nav>
                    <?php foreach ($nav as $route => $label): ?>
                        <a href="<?= h(url($route)) ?>"><?= h($label) ?></a>
                    <?php endforeach; ?>
                </nav>
                <div class="language-switch">
                    <?php foreach (APP_LANGS as $code): ?>
                        <a class="<?= $code === lang() ? 'active' : '' ?>" href="<?= h(url(route_path(), array_merge($_GET, ['lang' => $code]))) ?>"><?= h(strtoupper($code)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </header>
    <?php
    return ob_get_clean();
}

function render_public_footer(array $data): string
{
    $cols = [
        t('nav.categories') => [
            t('nav.buy') => 'inventory',
            t('nav.about') => 'about',
            t('nav.services') => 'services',
            t('nav.how') => 'how-to-buy',
        ],
        t('nav.auctions') => [
            t('home.auctions') => 'auctions',
            t('home.private') => 'inventory?private=1',
            t('profile.bids') => 'my-bids',
            t('profile.watchlist') => 'watchlist',
        ],
        t('nav.contact') => [
            t('nav.contact') => 'contact',
            'FAQs' => 'services',
            t('page.calculator') => 'calculator',
        ],
    ];

    ob_start();
    ?>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <?= render_logo() ?>
                <p><?= h(localized($data['settings']['address'])) ?></p>
                <p><?= h($data['settings']['email']) ?></p>
                <p><?= h($data['settings']['phone']) ?></p>
            </div>
            <?php foreach ($cols as $heading => $links): ?>
                <div class="footer-col">
                    <h3><?= h($heading) ?></h3>
                    <?php foreach ($links as $label => $route): ?>
                        <a href="<?= h(url($route)) ?>"><?= h($label) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <div class="footer-col">
                <h3>Social</h3>
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">Youtube</a>
            </div>
        </div>
        <div class="footer-bottom">©2026 <?= h(t('footer.rights')) ?></div>
    </footer>
    <?php
    return ob_get_clean();
}

function render_home(array $data): string
{
    $cars = filtered_cars($data);
    $featured = array_values(array_filter($cars, static fn ($car) => $car['featured']));
    $private = array_values(array_filter($cars, static fn ($car) => $car['private_sale']));

    ob_start();
    ?>
    <section class="hero">
        <div class="hero-bg" style="background-image:url('<?= h(asset_url('assets/extracted/06-075d3f1ebdf3.jpg')) ?>')"></div>
        <div class="container hero-inner">
            <div class="hero-copy">
                <h1><?= h(t('hero.title')) ?></h1>
                <p><?= h(t('hero.copy')) ?></p>
            </div>
            <form class="hero-search" action="<?= h(url('inventory')) ?>" method="get">
                <?= render_select('make', t('field.make'), available_values($cars, 'make'), $_GET['make'] ?? '') ?>
                <?= render_select('model', t('field.model'), available_values($cars, 'model'), $_GET['model'] ?? '') ?>
                <label>
                    <span><?= h(t('field.year_from')) ?></span>
                    <input type="number" name="year_from" placeholder="1960" min="1960" max="2026">
                </label>
                <label>
                    <span><?= h(t('field.year_to')) ?></span>
                    <input type="number" name="year_to" placeholder="2026" min="1960" max="2026">
                </label>
                <button class="btn primary" type="submit"><?= h(t('hero.cta')) ?></button>
            </form>
        </div>
    </section>

    <section class="container promo-band">
        <div>
            <h2><?= h(t('home.featured')) ?></h2>
            <p><?= h(t('home.services_copy')) ?></p>
            <a class="btn primary small" href="<?= h(url('inventory')) ?>"><?= h(t('home.more')) ?></a>
        </div>
        <img src="<?= h(asset_url('assets/extracted/17-28948be78c64.png')) ?>" alt="">
    </section>

    <?= render_car_section(t('home.featured'), $featured, 'inventory') ?>
    <?= render_auction_section($data['auctions']) ?>
    <?= render_car_section(t('home.private'), $private, 'inventory') ?>
    <?= render_brand_section() ?>

    <section class="service-band">
        <div class="container service-grid">
            <div class="service-intro">
                <h2><?= h(t('home.services_title')) ?></h2>
                <p><?= h(t('home.services_copy')) ?></p>
                <a href="<?= h(url('services')) ?>"><?= h(t('home.more')) ?> →</a>
            </div>
            <?php foreach (service_cards() as $card): ?>
                <article class="service-card">
                    <span><?= h($card['icon']) ?></span>
                    <h3><?= h(localized($card['title'])) ?></h3>
                    <p><?= h(localized($card['body'])) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container stats-band">
        <div>
            <strong>190+</strong>
            <span><?= h(lang() === 'hy' ? 'մասնակից երկրների շուկաներ' : 'source markets') ?></span>
        </div>
        <div>
            <strong>2.5M</strong>
            <span><?= h(lang() === 'hy' ? 'ակտիվ մեքենաներ' : 'active vehicles') ?></span>
        </div>
        <div>
            <strong>3,000</strong>
            <span><?= h(lang() === 'hy' ? 'շաբաթական նոր աճուրդներ' : 'weekly auctions') ?></span>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_select(string $name, string $label, array $options, mixed $selected = ''): string
{
    ob_start();
    ?>
    <label>
        <span><?= h($label) ?></span>
        <select name="<?= h($name) ?>">
            <option value=""><?= h(t('field.any')) ?></option>
            <?php foreach ($options as $option): ?>
                <option value="<?= h($option) ?>" <?= (string) $selected === (string) $option ? 'selected' : '' ?>><?= h($option) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php
    return ob_get_clean();
}

function render_car_section(string $title, array $cars, string $route): string
{
    ob_start();
    ?>
    <section class="container section-block">
        <div class="section-heading">
            <h2><?= h($title) ?></h2>
            <a href="<?= h(url($route)) ?>"><?= h(t('home.more')) ?> →</a>
        </div>
        <div class="car-grid">
            <?php foreach (array_slice($cars, 0, 4) as $car): ?>
                <?= render_car_card($car) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_car_card(array $car, bool $wide = false): string
{
    $image = $car['images'][0] ?? 'assets/extracted/03-17064740bf1e.jpg';
    $watched = is_watched((int) $car['id']);

    ob_start();
    ?>
    <article class="car-card <?= $wide ? 'wide' : '' ?>">
        <div class="car-media">
            <a href="<?= h(url('cars/' . $car['slug'])) ?>">
                <img src="<?= h(asset_url($image)) ?>" alt="<?= h(localized($car['title'])) ?>">
            </a>
            <form method="post" class="watch-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle_watch">
                <input type="hidden" name="car_id" value="<?= h($car['id']) ?>">
                <input type="hidden" name="return_to" value="<?= h($_SERVER['REQUEST_URI'] ?? url('')) ?>">
                <button class="<?= $watched ? 'active' : '' ?>" aria-label="<?= h($watched ? t('btn.unwatch') : t('btn.watch')) ?>">♡</button>
            </form>
        </div>
        <div class="car-body">
            <h3><a href="<?= h(url('cars/' . $car['slug'])) ?>"><?= h(localized($car['title'])) ?></a></h3>
            <p class="muted"><?= h($car['lot']) ?> · <?= h($car['auction']) ?></p>
            <p><?= h($car['sale_date']) ?> · <?= h($car['location']) ?></p>
            <div class="car-meta">
                <span><?= h($car['year']) ?></span>
                <span><?= h($car['body']) ?></span>
                <span><?= h($car['fuel']) ?></span>
            </div>
            <div class="car-actions">
                <strong><?= h(money($car['current_bid'])) ?></strong>
                <a class="btn ghost small" href="<?= h(url('cars/' . $car['slug'])) ?>"><?= h(t('btn.details')) ?></a>
            </div>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

function render_auction_section(array $auctions): string
{
    ob_start();
    ?>
    <section class="container section-block">
        <div class="section-heading">
            <h2><?= h(t('home.auctions')) ?></h2>
            <a href="<?= h(url('auctions')) ?>"><?= h(t('home.more')) ?> →</a>
        </div>
        <div class="auction-grid">
            <?php foreach ($auctions as $auction): ?>
                <article class="auction-card">
                    <img src="<?= h(asset_url($auction['image'])) ?>" alt="">
                    <div>
                        <h3><?= h($auction['name']) ?></h3>
                        <p><?= h($auction['address']) ?></p>
                        <div class="auction-foot">
                            <span><?= h($auction['starts_in']) ?></span>
                            <a class="btn ghost small" href="<?= h(url('inventory', ['auction' => $auction['name']])) ?>"><?= h(t('btn.details')) ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_brand_section(): string
{
    $brands = ['Ford', 'Nissan', 'Honda', 'Dodge', 'Mercedes-Benz', 'Toyota', 'Mazda', 'Audi'];
    ob_start();
    ?>
    <section class="container section-block">
        <div class="section-heading">
            <h2><?= h(t('home.brands')) ?></h2>
        </div>
        <div class="brand-strip">
            <?php foreach ($brands as $brand): ?>
                <a href="<?= h(url('inventory', ['make' => $brand])) ?>"><?= h($brand) ?></a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function service_cards(): array
{
    return [
        [
            'icon' => '$',
            'title' => ['hy' => 'Դեպոզիտի վճարում', 'en' => 'Deposit handling', 'ru' => 'Депозит'],
            'body' => ['hy' => 'Աճուրդային մասնակցության արագ ակտիվացում և վերահսկում։', 'en' => 'Fast auction access and controlled deposits.', 'ru' => 'Быстрый доступ к торгам и контроль депозита.'],
        ],
        [
            'icon' => '✓',
            'title' => ['hy' => 'Մեքենայի պատմություն', 'en' => 'Vehicle history', 'ru' => 'История авто'],
            'body' => ['hy' => 'Լոտի, վնասների, փաստաթղթերի և վաճառքի ստուգում։', 'en' => 'Lot, damage, paperwork and sale status checks.', 'ru' => 'Проверка лота, повреждений и документов.'],
        ],
        [
            'icon' => '↗',
            'title' => ['hy' => 'Աճուրդի մասնակցություն', 'en' => 'Bidding support', 'ru' => 'Сопровождение ставок'],
            'body' => ['hy' => 'Գնային ռազմավարություն և live bidding աջակցություն։', 'en' => 'Bid strategy and live auction support.', 'ru' => 'Стратегия цены и поддержка на торгах.'],
        ],
        [
            'icon' => '≡',
            'title' => ['hy' => 'Փաստաթղթեր և լոգիստիկա', 'en' => 'Docs and logistics', 'ru' => 'Документы и логистика'],
            'body' => ['hy' => 'Վճարում, բեռնում, առաքում և status tracking։', 'en' => 'Payment, pickup, shipping and status tracking.', 'ru' => 'Оплата, вывоз, доставка и отслеживание.'],
        ],
    ];
}

function render_inventory(array $data): string
{
    $cars = filtered_cars($data, $_GET);
    $allCars = filtered_cars($data);

    ob_start();
    ?>
    <section class="inventory-toolbar">
        <div class="container">
            <h1><?= h(t('inventory.title')) ?></h1>
            <div class="chips">
                <a class="chip" href="<?= h(url('inventory')) ?>"><?= h(t('field.any')) ?></a>
                <?php foreach (array_slice(available_values($allCars, 'body'), 0, 5) as $body): ?>
                    <a class="chip" href="<?= h(url('inventory', ['body' => $body])) ?>"><?= h($body) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="container inventory-layout">
        <aside class="filters-panel">
            <form method="get" action="<?= h(url('inventory')) ?>">
                <h2><?= h(t('inventory.filters')) ?></h2>
                <label>
                    <span><?= h(t('search.placeholder')) ?></span>
                    <input name="q" value="<?= h($_GET['q'] ?? '') ?>">
                </label>
                <?= render_select('make', t('field.make'), available_values($allCars, 'make'), $_GET['make'] ?? '') ?>
                <?= render_select('model', t('field.model'), available_values($allCars, 'model'), $_GET['model'] ?? '') ?>
                <?= render_select('body', 'Body', available_values($allCars, 'body'), $_GET['body'] ?? '') ?>
                <?= render_select('auction', 'Auction', available_values($allCars, 'auction'), $_GET['auction'] ?? '') ?>
                <div class="filter-row">
                    <label><span><?= h(t('field.year_from')) ?></span><input type="number" name="year_from" value="<?= h($_GET['year_from'] ?? '') ?>"></label>
                    <label><span><?= h(t('field.year_to')) ?></span><input type="number" name="year_to" value="<?= h($_GET['year_to'] ?? '') ?>"></label>
                </div>
                <button class="btn primary full" type="submit"><?= h(t('hero.cta')) ?></button>
            </form>
        </aside>
        <div class="inventory-results">
            <div class="results-head">
                <strong><?= count($cars) ?> <?= h(t('inventory.results')) ?></strong>
                <form method="get" action="<?= h(url('inventory')) ?>">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key !== 'sort'): ?>
                            <input type="hidden" name="<?= h($key) ?>" value="<?= h($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest"><?= h(t('sort.newest')) ?></option>
                        <option value="price" <?= ($_GET['sort'] ?? '') === 'price' ? 'selected' : '' ?>><?= h(t('sort.price')) ?></option>
                    </select>
                </form>
            </div>
            <div class="list-stack">
                <?php foreach ($cars as $car): ?>
                    <?= render_car_card($car, true) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_product(array $data, array $car): string
{
    $images = $car['images'];
    $related = array_values(array_filter(filtered_cars($data), static fn ($item) => (int) $item['id'] !== (int) $car['id']));

    ob_start();
    ?>
    <section class="container product-page">
        <div class="product-card">
            <div class="product-gallery">
                <h1><?= h(localized($car['title'])) ?></h1>
                <div class="main-photo">
                    <img data-gallery-main src="<?= h(asset_url($images[0])) ?>" alt="<?= h(localized($car['title'])) ?>">
                </div>
                <div class="thumb-row">
                    <?php foreach ($images as $image): ?>
                        <button type="button" data-gallery-thumb="<?= h(asset_url($image)) ?>">
                            <img src="<?= h(asset_url($image)) ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <aside class="bid-panel">
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="bid">
                    <input type="hidden" name="car_id" value="<?= h($car['id']) ?>">
                    <input type="hidden" name="return_to" value="<?= h($_SERVER['REQUEST_URI'] ?? '') ?>">
                    <label>
                        <span><?= h(t('btn.bid')) ?></span>
                        <input type="number" name="amount" value="<?= h($car['current_bid'] + 300) ?>" min="100">
                    </label>
                    <label><span><?= h(t('form.email')) ?></span><input type="email" name="email" required></label>
                    <button class="btn primary full" type="submit"><?= h(t('btn.bid')) ?></button>
                </form>
                <a class="btn danger full" href="<?= h(url('contact', ['car' => $car['id']])) ?>"><?= h(t('btn.buy_now')) ?> (<?= h(money($car['buy_now'])) ?>)</a>
                <div class="auction-info">
                    <h2><?= h(t('product.auction_info')) ?></h2>
                    <?= render_fact('Auction', $car['auction']) ?>
                    <?= render_fact('Location', $car['location']) ?>
                    <?= render_fact('Sale date', $car['sale_date']) ?>
                    <?= render_fact('Shipping fee', money($car['shipping_fee'])) ?>
                </div>
            </aside>
        </div>
        <div class="product-lower">
            <section class="spec-card">
                <h2><?= h(t('product.specs')) ?></h2>
                <div class="spec-grid">
                    <?= render_fact('Lot #', $car['lot']) ?>
                    <?= render_fact('VIN', $car['vin']) ?>
                    <?= render_fact('Odometer', $car['odometer']) ?>
                    <?= render_fact('Engine', $car['engine']) ?>
                    <?= render_fact('Transmission', $car['transmission']) ?>
                    <?= render_fact('Fuel', $car['fuel']) ?>
                    <?= render_fact('Body', $car['body']) ?>
                    <?= render_fact('Color', $car['color']) ?>
                </div>
            </section>
            <section class="lead-card">
                <h2><?= h(t('product.ask')) ?></h2>
                <?= render_lead_form((int) $car['id'], 'product') ?>
            </section>
        </div>
    </section>
    <?= render_car_section(t('product.related'), array_slice($related, 0, 3), 'inventory') ?>
    <?php
    return ob_get_clean();
}

function render_fact(string $label, mixed $value): string
{
    return '<div class="fact"><span>' . h($label) . '</span><strong>' . h($value) . '</strong></div>';
}

function render_lead_form(int $carId = 0, string $type = 'contact'): string
{
    ob_start();
    ?>
    <form method="post" class="lead-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="lead">
        <input type="hidden" name="type" value="<?= h($type) ?>">
        <input type="hidden" name="car_id" value="<?= h($carId) ?>">
        <input type="hidden" name="return_to" value="<?= h($_SERVER['REQUEST_URI'] ?? url('')) ?>">
        <label><span><?= h(t('form.name')) ?></span><input name="name" required></label>
        <label><span><?= h(t('form.email')) ?></span><input type="email" name="email" required></label>
        <label><span><?= h(t('form.phone')) ?></span><input name="phone"></label>
        <label><span><?= h(t('form.message')) ?></span><textarea name="message" rows="4"></textarea></label>
        <button class="btn primary full" type="submit"><?= h(t('form.send')) ?></button>
    </form>
    <?php
    return ob_get_clean();
}

function render_content_page(array $data, string $key): string
{
    $page = $data['content'][$key] ?? $data['content']['about'];
    ob_start();
    ?>
    <section class="page-hero compact">
        <div class="container">
            <h1><?= h(localized($page['title'])) ?></h1>
            <p><?= h(localized($page['body'])) ?></p>
        </div>
    </section>
    <section class="container content-grid">
        <?php foreach (service_cards() as $card): ?>
            <article class="service-card flat">
                <span><?= h($card['icon']) ?></span>
                <h2><?= h(localized($card['title'])) ?></h2>
                <p><?= h(localized($card['body'])) ?></p>
            </article>
        <?php endforeach; ?>
    </section>
    <?php
    return ob_get_clean();
}

function render_contact(array $data): string
{
    ob_start();
    ?>
    <section class="page-hero compact">
        <div class="container">
            <h1><?= h(t('nav.contact')) ?></h1>
            <p><?= h(localized($data['settings']['address'])) ?> · <?= h($data['settings']['phone']) ?></p>
        </div>
    </section>
    <section class="container contact-layout">
        <div class="contact-info">
            <h2><?= h($data['settings']['site_name']) ?></h2>
            <p><?= h($data['settings']['email']) ?></p>
            <p><?= h($data['settings']['phone']) ?></p>
            <p><?= h(localized($data['settings']['address'])) ?></p>
        </div>
        <div class="lead-card"><?= render_lead_form((int) ($_GET['car'] ?? 0), 'contact') ?></div>
    </section>
    <?php
    return ob_get_clean();
}

function render_calculator(): string
{
    ob_start();
    ?>
    <section class="page-hero compact">
        <div class="container">
            <h1><?= h(t('page.calculator')) ?></h1>
        </div>
    </section>
    <section class="container calculator-card">
        <div class="calc-form" data-calculator>
            <label><span>Vehicle price</span><input type="number" value="25000" data-calc-price></label>
            <label><span>Auction fee</span><input type="number" value="650" data-calc-fee></label>
            <label><span>Shipping</span><input type="number" value="1800" data-calc-shipping></label>
            <label><span>Service fee</span><input type="number" value="500" data-calc-service></label>
            <div class="calc-total">
                <span>Total estimate</span>
                <strong data-calc-total>$27,950</strong>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_auth(string $mode): string
{
    $isRegister = $mode === 'register';
    ob_start();
    ?>
    <section class="auth-page">
        <div class="auth-visual" style="background-image:url('<?= h(asset_url('assets/extracted/06-075d3f1ebdf3.jpg')) ?>')"></div>
        <form class="auth-card" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="<?= $isRegister ? 'register' : 'login' ?>">
            <h1><?= h($isRegister ? 'Register' : 'Log in') ?></h1>
            <?php if ($isRegister): ?>
                <label><span><?= h(t('form.name')) ?></span><input name="name" required></label>
            <?php endif; ?>
            <label><span><?= h(t('form.email')) ?></span><input type="email" name="email" required></label>
            <label><span>Password</span><input type="password" name="password" required></label>
            <button class="btn primary full" type="submit"><?= h($isRegister ? 'Register' : 'Log in') ?></button>
            <a href="<?= h(url($isRegister ? 'login' : 'register')) ?>"><?= h($isRegister ? 'Already have an account?' : 'Create account') ?></a>
        </form>
    </section>
    <?php
    return ob_get_clean();
}

function render_profile(array $data, string $tab = 'profile'): string
{
    $watched = array_values(array_filter($data['cars'], static fn ($car) => in_array((int) $car['id'], watched_ids(), true)));
    $bids = $data['bids'];

    ob_start();
    ?>
    <section class="container profile-page">
        <aside class="profile-side">
            <h1><?= h(t('profile.title')) ?></h1>
            <a class="<?= $tab === 'profile' ? 'active' : '' ?>" href="<?= h(url('profile')) ?>">Personal info</a>
            <a class="<?= $tab === 'watchlist' ? 'active' : '' ?>" href="<?= h(url('watchlist')) ?>"><?= h(t('profile.watchlist')) ?></a>
            <a class="<?= $tab === 'my-bids' ? 'active' : '' ?>" href="<?= h(url('my-bids')) ?>"><?= h(t('profile.bids')) ?></a>
            <a href="<?= h(url('saved-searches')) ?>"><?= h(lang() === 'hy' ? 'Պահպանված որոնումներ' : 'Saved searches') ?></a>
        </aside>
        <div class="profile-main">
            <?php if ($tab === 'watchlist'): ?>
                <h2><?= h(t('profile.watchlist')) ?></h2>
                <div class="car-grid compact">
                    <?php foreach ($watched as $car): ?>
                        <?= render_car_card($car) ?>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($tab === 'my-bids'): ?>
                <h2><?= h(t('profile.bids')) ?></h2>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Car</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($bids as $bid): $car = car_by_id($data, (int) $bid['car_id']); ?>
                                <tr><td><?= h($car ? localized($car['title']) : 'Vehicle') ?></td><td><?= h(money($bid['amount'])) ?></td><td><?= h($bid['status']) ?></td><td><?= h($bid['created_at']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <h2>Personal info</h2>
                <form class="settings-form">
                    <label><span><?= h(t('form.name')) ?></span><input value="Armen"></label>
                    <label><span><?= h(t('form.email')) ?></span><input value="<?= h($_SESSION['customer'] ?? 'armen@mail.com') ?>"></label>
                    <label><span><?= h(t('form.phone')) ?></span><input value="+374 99 000000"></label>
                    <button class="btn primary" type="button"><?= h(t('flash.saved')) ?></button>
                </form>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_admin_shell(string $title, string $content): string
{
    ob_start();
    ?>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-logo"><?= render_logo() ?></div>
            <nav>
                <?php foreach (admin_nav_items() as $route => $label): ?>
                    <a class="<?= route_path() === $route ? 'active' : '' ?>" href="<?= h(url($route)) ?>"><?= h($label) ?></a>
                <?php endforeach; ?>
            </nav>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="admin_logout">
                <button class="btn ghost full">Log out</button>
            </form>
        </aside>
        <div class="admin-main">
            <header class="admin-top">
                <h1><?= h($title) ?></h1>
                <a class="btn ghost small" href="<?= h(url('')) ?>">View site</a>
            </header>
            <?= $content ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function render_admin_login(): string
{
    ob_start();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin login | CarMark</title>
        <link rel="stylesheet" href="<?= h(asset_url('assets/css/styles.css')) ?>">
    </head>
    <body class="admin-login-body">
        <?= render_flashes() ?>
        <form class="admin-login-card" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="admin_login">
            <h1>Admin login</h1>
            <label><span>Email</span><input type="email" name="email" value="admin@carmark.am" required></label>
            <label><span>Password</span><input type="password" name="password" value="admin123" required></label>
            <button class="btn primary full">Log in</button>
        </form>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

function render_admin_dashboard(array $data): string
{
    $revenue = array_sum(array_map(static fn ($bid) => (float) $bid['amount'], $data['bids']));
    ob_start();
    ?>
    <section class="admin-metrics">
        <article><span>Vehicles</span><strong><?= count($data['cars']) ?></strong></article>
        <article><span>New leads</span><strong><?= count(array_filter($data['leads'], static fn ($lead) => $lead['status'] === 'new')) ?></strong></article>
        <article><span>Bids</span><strong><?= count($data['bids']) ?></strong></article>
        <article><span>Bid value</span><strong><?= h(money($revenue)) ?></strong></article>
    </section>
    <section class="admin-panel">
        <h2>Recent leads</h2>
        <?= render_leads_table($data, 5) ?>
    </section>
    <?php
    return ob_get_clean();
}

function render_admin_cars(array $data): string
{
    $edit = isset($_GET['edit']) ? car_by_id($data, (int) $_GET['edit']) : null;
    ob_start();
    ?>
    <section class="admin-panel split">
        <div>
            <h2>Vehicles</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Vehicle</th><th>Lot</th><th>Price</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($data['cars'] as $car): ?>
                            <tr>
                                <td><?= h(localized($car['title'])) ?></td>
                                <td><?= h($car['lot']) ?></td>
                                <td><?= h(money($car['current_bid'])) ?></td>
                                <td><?= h($car['status']) ?></td>
                                <td class="row-actions">
                                    <a href="<?= h(url('admin/cars', ['edit' => $car['id']])) ?>">Edit</a>
                                    <form method="post">
                                        <?= csrf_field() ?><input type="hidden" name="action" value="admin_delete_car"><input type="hidden" name="id" value="<?= h($car['id']) ?>">
                                        <button>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= render_admin_car_form($edit) ?>
    </section>
    <?php
    return ob_get_clean();
}

function render_admin_car_form(?array $car): string
{
    $car = $car ?? [
        'id' => 0,
        'slug' => '',
        'title' => ['hy' => '', 'en' => '', 'ru' => ''],
        'lot' => '',
        'vin' => '',
        'year' => 2026,
        'make' => '',
        'model' => '',
        'body' => '',
        'engine' => '',
        'transmission' => '',
        'fuel' => '',
        'color' => '',
        'condition' => '',
        'location' => '',
        'auction' => '',
        'sale_date' => '',
        'odometer' => '',
        'current_bid' => 0,
        'buy_now' => 0,
        'shipping_fee' => 0,
        'featured' => false,
        'private_sale' => false,
        'status' => 'published',
        'images' => ['assets/extracted/03-17064740bf1e.jpg'],
    ];

    ob_start();
    ?>
    <form class="admin-form" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="admin_save_car">
        <input type="hidden" name="id" value="<?= h($car['id']) ?>">
        <h2><?= $car['id'] ? 'Edit vehicle' : 'New vehicle' ?></h2>
        <label><span>Title HY</span><input name="title_hy" value="<?= h($car['title']['hy'] ?? '') ?>" required></label>
        <label><span>Title EN</span><input name="title_en" value="<?= h($car['title']['en'] ?? '') ?>" required></label>
        <label><span>Title RU</span><input name="title_ru" value="<?= h($car['title']['ru'] ?? '') ?>" required></label>
        <div class="form-grid">
            <?php foreach (['slug','lot','vin','year','make','model','body','engine','transmission','fuel','color','condition','location','auction','sale_date','odometer','current_bid','buy_now','shipping_fee','status'] as $field): ?>
                <label><span><?= h(ucwords(str_replace('_', ' ', $field))) ?></span><input name="<?= h($field) ?>" value="<?= h($car[$field] ?? '') ?>"></label>
            <?php endforeach; ?>
        </div>
        <label><span>Images comma-separated</span><textarea name="images" rows="3"><?= h(implode(', ', $car['images'] ?? [])) ?></textarea></label>
        <div class="check-row">
            <label><input type="checkbox" name="featured" <?= !empty($car['featured']) ? 'checked' : '' ?>> Featured</label>
            <label><input type="checkbox" name="private_sale" <?= !empty($car['private_sale']) ? 'checked' : '' ?>> Private sale</label>
        </div>
        <button class="btn primary full">Save vehicle</button>
    </form>
    <?php
    return ob_get_clean();
}

function render_admin_leads(array $data): string
{
    ob_start();
    ?>
    <section class="admin-panel">
        <h2>Leads</h2>
        <?= render_leads_table($data) ?>
    </section>
    <?php
    return ob_get_clean();
}

function render_leads_table(array $data, ?int $limit = null): string
{
    $leads = array_reverse($data['leads']);
    if ($limit) {
        $leads = array_slice($leads, 0, $limit);
    }

    ob_start();
    ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Name</th><th>Contact</th><th>Vehicle</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($leads as $lead): $car = car_by_id($data, (int) $lead['car_id']); ?>
                    <tr>
                        <td><?= h($lead['created_at']) ?></td>
                        <td><?= h($lead['name']) ?></td>
                        <td><?= h($lead['email']) ?><br><?= h($lead['phone']) ?></td>
                        <td><?= h($car ? localized($car['title']) : '-') ?></td>
                        <td>
                            <form method="post" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="admin_lead_status">
                                <input type="hidden" name="id" value="<?= h($lead['id']) ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <?php foreach (['new','contacted','won','lost'] as $status): ?>
                                        <option value="<?= h($status) ?>" <?= $lead['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

function render_admin_content(array $data): string
{
    ob_start();
    ?>
    <section class="admin-panel cards-list">
        <?php foreach ($data['content'] as $key => $page): ?>
            <form class="admin-form" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="admin_save_content">
                <input type="hidden" name="key" value="<?= h($key) ?>">
                <h2><?= h($key) ?></h2>
                <?php foreach (APP_LANGS as $code): ?>
                    <label><span>Title <?= h(strtoupper($code)) ?></span><input name="title_<?= h($code) ?>" value="<?= h($page['title'][$code] ?? '') ?>"></label>
                    <label><span>Body <?= h(strtoupper($code)) ?></span><textarea name="body_<?= h($code) ?>" rows="4"><?= h($page['body'][$code] ?? '') ?></textarea></label>
                <?php endforeach; ?>
                <button class="btn primary">Save content</button>
            </form>
        <?php endforeach; ?>
    </section>
    <?php
    return ob_get_clean();
}

function render_admin_settings(array $data): string
{
    $settings = $data['settings'];
    ob_start();
    ?>
    <section class="admin-panel">
        <form class="admin-form narrow" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="admin_save_settings">
            <label><span>Site name</span><input name="site_name" value="<?= h($settings['site_name']) ?>"></label>
            <label><span>Email</span><input name="email" value="<?= h($settings['email']) ?>"></label>
            <label><span>Phone</span><input name="phone" value="<?= h($settings['phone']) ?>"></label>
            <label><span>Address HY</span><input name="address_hy" value="<?= h($settings['address']['hy']) ?>"></label>
            <label><span>Address EN</span><input name="address_en" value="<?= h($settings['address']['en']) ?>"></label>
            <label><span>Address RU</span><input name="address_ru" value="<?= h($settings['address']['ru']) ?>"></label>
            <button class="btn primary">Save settings</button>
        </form>
    </section>
    <?php
    return ob_get_clean();
}
