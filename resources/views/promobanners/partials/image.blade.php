<div class="h-20 my-4" style="background: url({{ config('rapidez.media_url') }}/amasty/ampromobanners/{{$banner->banner_img}})">
    <div class="max-w-7xl h-auto mx-auto py-3 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center">
                <p class="ml-3 font-medium text-white truncate">
                    <a href="{{ to($banner->banner_link) }}">
                        <span>
                            @lang($banner->banner_title)
                        </span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
