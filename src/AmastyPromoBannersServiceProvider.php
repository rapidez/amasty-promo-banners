<?php

namespace Rapidez\AmastyPromoBanners;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Rapidez\AmastyPromoBanners\Models\Banner;
use Rapidez\AmastyPromoBanners\ViewDirectives\BannersDirective;

class AmastyPromoBannersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->loadViews()
            ->publishViews()
            ->configureModule()
            ->registerBannerComponent()
            ->getAmongProductsBannersOnCategory();
    }

    public function registerBannerComponent(): self
    {
        Blade::directive('banners', function ($expression) {
            return "<?php echo app('banners-directive')->render($expression)?>";
        });

        $this->app->bind('banners-directive', BannersDirective::class);

        return $this;
    }

    public function loadViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'AmastyPromoBanners');

        return $this;
    }

    public function publishViews(): self
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/AmastyPromoBanners'),
        ], 'views');

        return $this;
    }

    public function configureModule(): self
    {
        $this->publishes([
            __DIR__.'/../config/amastypromobanners.php' => config_path('amastypromobanners.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../config/amastypromobanners.php',
            'amastypromobanners'
        );

        return $this;
    }

    public function getAmongProductsBannersOnCategory(): self
    {
        View::composer('rapidez::category.overview', function ($view) {
            $banners = Banner::getForLocationAndCategory('among_products', config('frontend.category.entity_id'))
                ->get(['after_n_product_row', 'banner_img', 'banner_link', 'banner_title'])
                ->keyBy('after_n_product_row');

            config(['frontend.category.banners' => $banners]);
        });

        return $this;
    }
}
