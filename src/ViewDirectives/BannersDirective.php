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
    public function render($location, $rule)
    {
        return Cache::rememberForever(
            'banners.'.md5(serialize(func_get_args())),
            function () use ($location, $rule) {
                $positions = config('amastypromobanners.locations');

                switch (gettype($rule)) {
                    case 'string':
                        $banners = Banner::getForLocationAndSku($location, $rule);
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

                if (!isset($positions[$location]) || !$banners->count()) {
                    return;
                }

                $html = '';
                foreach ($banners->get() as $banner) {
                    if ($this->checkSchedulingDate($banner)) {
                        $html .= view('AmastyPromoBanners::promobanners.partials.'.$banner->banner_type, ['banner' => $banner])->render();
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
        return Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->setTimezone($timezone), $timezone)->between($from, $to);
    }
}