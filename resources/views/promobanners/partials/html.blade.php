<div class="bg-gray-200 rounded-lg my-4">
    <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center justify-between">
                @if(str_contains($banner->html_text, 'widget'))
                    Widgets not implemented yet.
                @else
                    {!! $banner->html_text !!}
                @endif
            </div>
        </div>
    </div>
</div>
