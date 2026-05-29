<?php
/**
 * Artesaos SEOTools Configuration — MotoPartHub
 *
 * Package: artesaos/seotools
 * Docs: https://github.com/artesaos/seotools
 *
 * This config controls default meta tags, Open Graph, Twitter Cards,
 * and JSON-LD for the entire application. Page-specific overrides are
 * handled by App\Services\SEO\MetaService::setFor*() methods.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Inertia.js support
    |--------------------------------------------------------------------------
    | Set to true only if you are using Inertia.js. This project uses Blade.
    */
    'inertia' => env('SEO_TOOLS_INERTIA', false),

    /*
    |--------------------------------------------------------------------------
    | Meta Tags
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'defaults' => [
            // Default title when no page-specific title is set
            'title'        => env('APP_NAME', 'MotoPartHub') . ' — Sparepart Motor Premium Indonesia',

            // Prepend site name before page title: false = "Page Title — Site"
            // true = "Site — Page Title"
            'titleBefore'  => false,

            // Default meta description (≤155 chars for Google snippet)
            'description'  => 'Destinasi utama suku cadang OEM & aftermarket motor terpercaya di Indonesia. Honda, Yamaha, Kawasaki, Suzuki & lebih banyak merek. Harga kompetitif, stok lengkap.',

            // Title separator: used between page title and site name
            'separator'    => ' — ',

            // Default keywords (page-specific keywords are added on top)
            'keywords'     => [
                'sparepart motor',
                'suku cadang motor',
                'sparepart OEM',
                'aksesoris motor',
                'suku cadang Honda',
                'suku cadang Yamaha',
                'MotoPartHub',
                'online motor parts Indonesia',
            ],

            // Canonical URL — 'current' uses Url::current() (recommended)
            'canonical'    => 'current',

            // Robots directive: 'all' = index,follow (safe default for production)
            'robots'       => 'all',
        ],

        /*
        |--------------------------------------------------------------------------
        | Webmaster Verification Tags
        |--------------------------------------------------------------------------
        | Add your verification tokens from each platform's Search Console.
        | Leave as null to omit that tag from the <head>.
        */
        'webmaster_tags' => [
            'google'    => env('GOOGLE_SITE_VERIFICATION', null),
            'bing'      => env('BING_SITE_VERIFICATION', null),
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Protocol
    |--------------------------------------------------------------------------
    | Controls og:* meta tags used by Facebook, LinkedIn, WhatsApp previews.
    | @see https://ogp.me/
    */
    'opengraph' => [
        'defaults' => [
            'title'       => env('APP_NAME', 'MotoPartHub') . ' — Sparepart Motor Premium',
            'description' => 'Destinasi utama suku cadang OEM & aftermarket motor terpercaya di Indonesia. Harga kompetitif, pengiriman cepat ke seluruh Indonesia.',
            'url'         => null,          // null = Url::current()
            'type'        => 'website',
            'site_name'   => env('APP_NAME', 'MotoPartHub'),
            'images'      => [
                // Default OG share image (1200×630px recommended)
                // Uses APP_URL so it works in production
                env('APP_URL', 'http://localhost') . '/images/og-default.webp',
            ],

            // Locale for OG tags
            'locale'      => 'id_ID',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter / X Cards
    |--------------------------------------------------------------------------
    | Controls twitter:* meta tags used by X (formerly Twitter).
    | @see https://developer.twitter.com/en/docs/twitter-for-websites/cards
    */
    'twitter' => [
        'defaults' => [
            // Card type: 'summary' (small image) or 'summary_large_image' (large banner)
            'card'    => 'summary_large_image',

            // Your site's X/Twitter handle (without @). Leave commented to omit.
            // 'site' => '@MotoPartHub',

            // Creator handle (optional, for article authors)
            // 'creator' => '@MotoPartHub',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-LD Structured Data
    |--------------------------------------------------------------------------
    | Controls the default JSON-LD <script> tag in <head>.
    | Page-specific JSON-LD (Product, BreadcrumbList) is pushed via
    | @push('styles') in individual views from SchemaService.
    */
    'json-ld' => [
        'defaults' => [
            'title'       => env('APP_NAME', 'MotoPartHub') . ' — Sparepart Motor Premium Indonesia',
            'description' => 'Destinasi utama suku cadang OEM & aftermarket motor terpercaya di Indonesia.',
            'url'         => null,          // null = Url::current()
            'type'        => 'WebSite',
            'images'      => [
                env('APP_URL', 'http://localhost') . '/images/og-default.webp',
            ],
        ],
    ],
];
