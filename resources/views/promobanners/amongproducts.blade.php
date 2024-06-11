@if(!isset($slider))
    <component
        v-if="key = Object.keys(config.category.banners).find(key => key.split(',').find(splittedKey => splittedKey - 1 == getListingCount(count - 1, {{ Rapidez::config('catalog/frontend/grid_per_page', 12) }})))" 
        :is="config.category.banners[key].banner_link ? 'a' : 'div'"
        :href="config.category.banners[key].banner_link | url"
        class="flex-none w-1/2 sm:w-1/3 lg:w-1/4 px-1 my-1 relative"
    >
        <img :src="'{{ config('rapidez.media_url') }}/amasty/ampromobanners/' + config.category.banners[key].banner_img" alt="">
        <div
            v-if="config.category.banners[key].banner_title"
            class="absolute inset-0 text-center text-white font-bold"
        >
            @{{ config.category.banners[key].banner_title }}
        </div>
    </component>
@endif
