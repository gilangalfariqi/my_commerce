<?php

namespace App\Services\SEO;

use App\Models\Product;
use App\Models\Category;

class SchemaService
{
    public function productSchema(Product $product): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $product->name,
            'sku'      => $product->sku,
            'description' => strip_tags($product->description ?? ''),
            'offers'   => [
                '@type'         => 'Offer',
                'priceCurrency' => 'IDR',
                'price'         => $product->price,
                'availability'  => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url'           => route('products.show', $product->slug),
            ],
        ];

        if ($product->primaryImage) {
            $schema['image'] = $product->primaryImage->url;
        }

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

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

        return json_encode([
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function organizationSchema(): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('app.name'),
            'url'      => config('app.url'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
