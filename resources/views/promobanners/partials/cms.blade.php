@php
$locations = [];
@endphp
<div class="bg-gray-200 rounded-lg my-4 {{ $locations[$location] ?? '' }}">
    <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center justify-between text-xl font-extrabold tracking-tight text-gray-600">
                @block($banner->cms_block)
            </div>
        </div>
    </div>
</div>
