# Rapidez Amasty Promo Banners

## Requirements

You need to have the [Amasty Promo Banners](https://amasty.com/promo-banners-for-magento-2.html) module installed and configured within your Magento 2 installation.

## Installation

```
composer require rapidez/amasty-promo-banners
```

If you haven't published the Rapidez views yet, you can publish them with:
```
php artisan vendor:publish --provider="Rapidez\Core\RapidezServiceProvider" --tag=views
```

## Usage

### Categories

Add `@banners('category_page', $category->entity_id)` where you'd like to display your banners, add the specified location to make sure the banner shows up on the location you've specified in magento backend. The possible locations are listed at [locations](#Locations). For categories, you can just pass the category id in the blade directive to make sure it only displays for the selected categories in the Magento backend.

### Products

#### With product rules

If you want the banners to be rendered conditional (based on magento rules), you can pass the product as object to the banners directive:
`@banners('prod_page', $product)`.
This will display the banners for all products that passes the conditions.

#### Without product rules

If you just want to show the banner on some product pages, you can pass the SKU in the banners directive:
`@banners('prod_page', $product->sku)`
This will only display the banner for the specified SKUs in the Magento backend.

### Among category products

Until [this PR](https://github.com/appbaseio/reactivesearch/pull/1932) is merged and released you'll need to install the fork:
```
yarn add 'https://gitpkg.now.sh/api/pkg?url=royduin/reactivesearch/packages/vue&commit=patch-4&scripts.postinstall=yarn%20install%20--ignore-scripts%20%26%26%20yarn%20run%20build-es&scripts.build-es=nps%20build.es'
```

Just add the view within the `renderItem` slot in `listing/partials/item.blade.php` and add `count` to the `slot-scope`
```
@include('AmastyPromoBanners::promobanners.amongproducts')
```

## Views

If you need to change the views you can publish them with:
```
php artisan vendor:publish --provider="Rapidez\AmastyPromoBanners\AmastyPromoBannersServiceProvider" --tag=views
```

## Locations

These are the locations that are configurable in the magento backend for each banner.
Backend label | code key
--- | ---
Above Cart | `above_cart`
Cart Page (Below Total) | `checkout_below_total`
Sidebar Additional | `sidebar_right`
Sidebar Main | `sidebar_left`
Product Page (Top) | `prod_page`
Product Page (Bottom) | `prod_page_bottom`
Product Page (Below Cart Button) | `prod_page_below_cart`
Category Page (Top) | `category_page`
Category Page (Bottom) | `category_page_bottom`
Category Page (Below Add to Cart Button) | `category_page_below_add_to_cart`
Catelog Search (Top) | `catalog_search_top`
On Top of Page | `top_page`
Home Page under Menu | `top_index`
Among Category Products | `among_products`

## Note

Not all features are implemented yet! For example: cart rules, show on search, etc.
