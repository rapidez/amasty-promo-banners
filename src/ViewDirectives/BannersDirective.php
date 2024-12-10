<?php

namespace Rapidez\AmastyPromoBanners\ViewDirectives;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Rapidez\AmastyPromoBanners\Models\Banner;
use Rapidez\Core\Models\Config;

class BannersDirective
{
    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render($location, $rule = '')
    {
        return Cache::rememberForever(
            'banners.'.md5(serialize(func_get_args())),
            function () use ($location, $rule) {
                $positions = config('rapidez.amastypromobanners.locations');

                switch (gettype($rule)) {
                    case 'string':
                        $banners = $rule !== '' ? Banner::getForLocationAndSku($location, $rule) : Banner::getForLocation($location);
                        break;
                    case 'object':
                        $banners = Banner::getForLocationAndProductRules($location, $rule);
                        break;
                    case 'integer':
                        $banners = Banner::getForLocationAndCategory($location, $rule);
                        break;
                    default:
                        $banners = new Collection();
                        break;
                }

                if (is_null($banners) || !isset($positions[$location]) || !$banners->count()) {
                    return false;
                }

                $html = '';
                foreach ($banners->get() as $banner) {
                    if ($this->checkSchedulingDate($banner)) {
                        $html .= view('AmastyPromoBanners::promobanners.partials.'.$banner->banner_type, compact('banner', 'location'))->render();
                    }
                }

                return $html;
            }
        );
    }

    private function checkSchedulingDate($banner)
    {
        if (is_null($banner->from_date) || is_null($banner->to_date)) {
            return true;
        }

        $timezone = Config::getCachedByPath('general/locale/timezone');
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $banner->from_date, $timezone);
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $banner->to_date, $timezone);

        return Carbon::now()->setTimezone($timezone)->between($from, $to);
    }
}
