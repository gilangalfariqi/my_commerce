<?php

namespace App\Services\SEO;

use App\Models\BikeBrand;
use App\Models\Category;
use App\Models\Product;

/**
 * Schema.org JSON-LD generator for MotoPartHub.
 *
 * Generates structured data for:
 *   - Product (schema.org/AutoPart for automotive SEO)
 *   - BreadcrumbList
 *   - Organization
 *   - ItemList (for catalog pages)
 */
class SchemaService
{
    /**
     * Generate schema.org/AutoPart JSON-LD for a motorcycle spare part.
     *
     * Uses the AutoPart sub-type of Product for maximum automotive SEO signal.
     * Includes OEM part number (mpn), compatible vehicles (vehicleEngine),
     * and proper Offer node with IDR pricing.
     */
    public function productSchema(Product $product): string
    {
        $imageUrl = $product->getFirstMediaUrl('product-images', 'hd')
            ?: $product->getFirstMediaUrl('product-images')
            ?: $product->primaryImage?->url
            ?: '';

        // Build compatible vehicle array from fitments
        $compatibleVehicles = $product->fitments->map(function ($model) {
            return [
                '@type'       => 'Car',          // schema.org/Car (closest to motorcycle; MotorizedBicycle not widely indexed)
                'vehicleModelDate' => $model->pivot->year ?? null,
                'brand'       => [
                    '@type' => 'Brand',
                    'name'  => $model->bikeBrand?->name,
                ],
                'model'       => $model->name,
                'vehicleEngine' => $model->engine_cc ? [
                    '@type'           => 'EngineSpecification',
                    'engineDisplacement' => [
                        '@type'    => 'QuantitativeValue',
                        'value'    => (int) filter_var($model->engine_cc, FILTER_SANITIZE_NUMBER_INT),
                        'unitCode' => 'CMQ',
                    ],
                ] : null,
            ];
        })->filter()->values()->toArray();

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => ['Product', 'AutoPart'],
            'name'        => $product->name,
            'sku'         => $product->sku,
            'mpn'         => $product->sku,          // OEM Part Number
            'description' => strip_tags($product->description ?? $product->short_description ?? ''),
            'url'         => route('products.show', $product->slug),
            'offers'      => [
                '@type'         => 'Offer',
                'priceCurrency' => 'IDR',
                'price'         => $product->getFinalPrice(),
                'priceValidUntil' => now()->addMonths(3)->format('Y-m-d'),
                'availability'  => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => config('app.name'),
                    'url'   => config('app.url'),
                ],
                'url'           => route('products.show', $product->slug),
            ],
        ];

        if ($imageUrl) {
            $schema['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $imageUrl,
                'width'  => 1200,
                'height' => 1200,
            ];
        }

        if ($product->weight > 0) {
            $schema['weight'] = [
                '@type'    => 'QuantitativeValue',
                'value'    => $product->weight,
                'unitCode' => 'GRM',
            ];
        }

        if (! empty($compatibleVehicles)) {
            $schema['isCompatibleWith'] = $compatibleVehicles;
        }

        if ($product->categories->isNotEmpty()) {
            $schema['category'] = $product->categories->pluck('name')->implode(' > ');
        }

        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) .
            '</script>';
    }

    /**
     * BreadcrumbList schema for page navigation trail.
     */
    public function breadcrumbSchema(array $items): string
    {
        $listItems = [];
        foreach ($items as $i => $item) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $item['name'],
                'item'     => $item['url'],
            ];
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];

        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
            '</script>';
    }

    /**
     * Organization schema for the store (for homepage and global <head>).
     */
    public function organizationSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'AutoPartsStore',
            'name'     => config('app.name'),
            'url'      => config('app.url'),
            'logo'     => config('app.url') . '/images/logo.png',
            'contactPoint' => [
                '@type'       => 'ContactPoint',
                'telephone'   => '+62-821-7412-8947',
                'contactType' => 'customer service',
                'contactOption' => 'TollFree',
                'areaServed'  => 'ID',
                'availableLanguage' => ['Indonesian', 'English'],
            ],
            'address' => [
                '@type'           => 'PostalAddress',
                'addressCountry'  => 'ID',
                'addressLocality' => 'Indonesia',
            ],
            'sameAs' => [
                // Add your social media URLs here
            ],
        ];

        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
            '</script>';
    }

    /**
     * ItemList schema for catalog pages (improves Google Shopping indexation).
     */
    public function catalogItemListSchema(iterable $products, string $pageTitle): string
    {
        $items = [];
        foreach ($products as $i => $product) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'url'      => route('products.show', $product->slug),
                'name'     => $product->name,
            ];
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'name'            => $pageTitle,
            'description'     => 'Katalog sparepart motor ' . $pageTitle,
            'itemListElement' => $items,
        ];

        return '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
            '</script>';
    }
}
