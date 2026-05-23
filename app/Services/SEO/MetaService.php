<?php

namespace App\Services\SEO;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;

class MetaService
{
    public function setDefault(): void
    {
        $storeName = config('app.name', 'MyCommerce');
        $desc      = 'Belanja online terpercaya dengan produk berkualitas dan harga terbaik.';

        SEOMeta::setTitle($storeName);
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical(url()->current());
        OpenGraph::setTitle($storeName);
        OpenGraph::setDescription($desc);
        OpenGraph::setType('website');
        OpenGraph::setUrl(url()->current());
        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($storeName);
        TwitterCard::setDescription($desc);
    }

    public function setForProduct(\App\Models\Product $product): void
    {
        $image = $product->primaryImage?->url;
        $desc  = mb_substr(strip_tags($product->short_description ?? $product->description ?? ''), 0, 155);

        SEOMeta::setTitle($product->name . ' - ' . config('app.name'));
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical(route('products.show', $product->slug));
        OpenGraph::setTitle($product->name);
        OpenGraph::setDescription($desc);
        OpenGraph::setType('product');
        OpenGraph::setUrl(route('products.show', $product->slug));
        if ($image) OpenGraph::addImage($image);
        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($product->name);
        TwitterCard::setDescription($desc);
        if ($image) TwitterCard::setImage($image);
    }

    public function setForCategory(\App\Models\Category $category): void
    {
        $desc = mb_substr(strip_tags($category->description ?? 'Jelajahi koleksi ' . $category->name . ' kami.'), 0, 155);
        SEOMeta::setTitle($category->name . ' - ' . config('app.name'));
        SEOMeta::setDescription($desc);
        SEOMeta::setCanonical(route('categories.show', $category->slug));
        OpenGraph::setTitle($category->name);
        OpenGraph::setDescription($desc);
        OpenGraph::setType('website');
    }

    public function setCustom(string $title, string $description, ?string $canonical = null, ?string $image = null): void
    {
        SEOMeta::setTitle($title . ' - ' . config('app.name'));
        SEOMeta::setDescription(mb_substr($description, 0, 155));
        SEOMeta::setCanonical($canonical ?? url()->current());
        OpenGraph::setTitle($title);
        OpenGraph::setDescription(mb_substr($description, 0, 155));
        if ($image) OpenGraph::addImage($image);
        TwitterCard::setTitle($title);
        TwitterCard::setDescription(mb_substr($description, 0, 155));
        if ($image) TwitterCard::setImage($image);
    }
}
