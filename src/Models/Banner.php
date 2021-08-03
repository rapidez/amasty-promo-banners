<?php

namespace Rapidez\AmastyPromoBanners\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\AmastyPromoBanners\Models\Traits\BannerConditions;
use Rapidez\Core\Models\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class Banner extends Model
{
    use BannerConditions;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'amasty_banner_rule';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsActiveScope());
        static::addGlobalScope('default', function (Builder $builder) {
            $builder->orderBy('sort_order', 'asc');
        });
    }
}
