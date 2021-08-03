<?php

namespace Rapidez\AmastyPromoBanners\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Rapidez\AmastyPromoBanners\Models\Banner;

trait BannerConditions
{
    private static string $combineType = 'Magento\SalesRule\Model\Rule\Condition\Product\Combine';

    public static function getForLocationAndCategory($location, $categoryId): Builder
    {
        $positions = config('amastypromobanners.locations');
        $banners = self::whereRaw('FIND_IN_SET(?, banner_position) > 0', [$positions[$location]]);
        $foundBanners = new Collection();

        foreach ($banners->get() as $banner) {
            if (empty($banner->cats) || in_array($categoryId, explode(',', $banner->cats))) {
                $foundBanners->push($banner->id);
            }
        }

        return Banner::whereIn('id', $foundBanners);
    }

    public static function getForLocationAndSku($location, $sku): Builder
    {
        $positions = config('amastypromobanners.locations');

        return self::whereRaw('FIND_IN_SET(?, show_on_products) > 0', [$sku])
        ->whereRaw('FIND_IN_SET(?, banner_position) > 0', [$positions[$location]]);
    }

    public static function getForLocationAndProductRules($location, $product): Builder
    {
        $positions = config('amastypromobanners.locations');
        $banners = self::selectRaw('id, actions_serialized')->whereRaw('FIND_IN_SET(?, banner_position) > 0', [$positions[$location]]);
        $foundBanners = new Collection();
        foreach ($banners->get() as $banner) {
            $rules = json_decode($banner->actions_serialized);
            if (!isset($rules->conditions) || self::checkConditionsRecursive($rules, $product, $rules->aggregator)) {
                $foundBanners->push($banner->id);
            }
        }

        return Banner::whereIn('id', $foundBanners);
    }

    public static function getForLocation($location)
    {
        $positions = config('amastypromobanners.locations');
        $banners = self::whereRaw('FIND_IN_SET(?, banner_position) >0', [$positions[$location]]);

        return $banners;
    }

    private static function checkConditionsRecursive($conditions, $product, $aggregator): bool
    {
        $checks = new Collection();

        if (isset($conditions->type) && $conditions->type == self::$combineType) {
            return self::checkConditionsRecursive($conditions->conditions, $product, $conditions->aggregator);
        }

        foreach ($conditions as $condition) {
            if (isset($condition->type) && $condition->type == self::$combineType) {
                $checks->push(self::checkConditionsRecursive($condition->conditions, $product, $condition->aggregator));
            }

            if (isset($condition->type) && $condition->type !== self::$combineType) {
                $checks->push(self::checkIfConditionIsTrue($condition->operator, $condition->value, $product->getRawOriginal($condition->attribute)));
            }
        }

        return $aggregator == 'any' ? $checks->contains(true) : $checks->every(fn ($value, $key) => $value);
    }

    private static function checkIfConditionIsTrue($operator, $value1, $value2): bool
    {
        $conditionValue = collect($value1);
        $productValue = collect(explode(',', $value2));

        return [
            '=='  => $conditionValue == $productValue,
            '!='  => $conditionValue != $productValue,
            '{}'  => $conditionValue->intersect($productValue)->count(),
            '!{}' => !$conditionValue->intersect($productValue)->count(),
            '()'  => $conditionValue->intersect($productValue)->count(),
            '!()' => !$conditionValue->intersect($productValue)->count(),
            '<'   => $productValue < $conditionValue,
            '>'   => $productValue > $conditionValue,
            '>='  => $productValue >= $conditionValue,
            '<='  => $productValue <= $conditionValue,
        ][$operator] ?? false;
    }
}
