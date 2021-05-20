@foreach($banners->get() as $banner)
    @include('AmastyPromoBanners::promobanners.partials.'.$banner->banner_type)
@endforeach
