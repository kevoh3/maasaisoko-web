@extends('layouts.frontend')

@php
    $gtext = gtext();
    $pageName = $selectedPath ?: ($title ?? __('County'));
    // guard for includes that may expect $counties
    $counties = $counties ?? collect();
@endphp

@section('title', $pageName)

@section('meta-content')
    <meta name="keywords" content="{{ $pageName }}" />
    <meta name="description" content="{{ $pageName }}" />
    <meta property="og:title" content="{{ $pageName }}" />
    <meta property="og:site_name" content="{{ $gtext['site_name'] }}" />
    <meta property="og:description" content="{{ $pageName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ asset('public/media/'.$gtext['og_image']) }}" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="315" />
    @if($gtext['fb_publish'] == 1)
        <meta name="fb:app_id" property="fb:app_id" content="{{ $gtext['fb_app_id'] }}" />
    @endif
    <meta name="twitter:card" content="summary_large_image">
    @if($gtext['twitter_publish'] == 1)
        <meta name="twitter:site" content="{{ $gtext['twitter_id'] }}">
        <meta name="twitter:creator" content="{{ $gtext['twitter_id'] }}">
    @endif
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $pageName }}">
    <meta name="twitter:description" content="{{ $pageName }}">
    <meta name="twitter:image" content="{{ asset('public/media/'.$gtext['og_image']) }}">
@endsection

@section('header')
    @include('frontend.partials.header')
@endsection

@section('content')

    <main class="main">
        <!-- Page Breadcrumb -->
        <div class="breadcrumb-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $pageName }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-title">
                            <h1>{{ $pageName }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Breadcrumb/ -->

        <!-- Inner Section -->
        <section class="inner-section inner-section-bg">
            <div class="container">
                @if($brand_variation == 'left_sidebar')
                    <div class="row">
                        <div class="col-lg-3">
                            @include('frontend.partials.sidebar')
                        </div>
                        <div class="col-lg-9">
                            @elseif($brand_variation == 'right_sidebar')
                                <div class="row">
                                    <div class="col-lg-9">
                                        @endif

                                        <div class="filter-card">
                                            <div class="row">
                                                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                                    <div class="filter_select">
                                                        <select name="num" id="num" class="form-select form-select-sm">
                                                            <option value="">{{ __('Showing') }}</option>
                                                            @if(($brand_variation == 'left_sidebar') || ($brand_variation == 'right_sidebar'))
                                                                <option value="9">9</option>
                                                                <option value="15">15</option>
                                                                <option value="24">24</option>
                                                            @else
                                                                <option value="12">12</option>
                                                                <option value="20">20</option>
                                                                <option value="28">28</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                                                    <div class="sort_by_select">
                                                        <select name="sortby" id="sortby" class="form-select form-select-sm">
                                                            <option value="default_sorting" selected="">{{ __('Default') }}</option>
                                                            <option value="date_asc">Oldest</option>
                                                            <option value="date_desc">Newest</option>
                                                            <option value="name_asc">Name: A-Z</option>
                                                            <option value="name_desc">Name : Z-A</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- (Optional) price filter controls, matching Brand IDs so JS works the same --}}
                                            {{-- geo id for AJAX --}}
                                            <input type="hidden" id="geo" value="{{ (int)($selectedGeo ?? 0) }}">

                                            {{-- Child geos list --}}
                                            <div id="child-geos" class="my-3"></div>

                                        {{-- geo id for AJAX --}}
                                        <input type="hidden" id="geo" value="{{ (int)($selectedGeo ?? 0) }}">

                                        <div id="tp_datalist">
                                            @include('frontend.partials.county-grid')
                                        </div>

                                        @if($brand_variation == 'left_sidebar')
                                    </div>
                                </div>
                            @elseif($brand_variation == 'right_sidebar')
                        </div>
                        <div class="col-lg-3">
                            @include('frontend.partials.sidebar')
                        </div>
                    </div>
                @endif
            </div>
        </section>
        <!-- /Inner Section/ -->
    </main>

@endsection

@push('scripts')
    <script type="text/javascript">

        window.COUNTY_GRID_URL   = "{{ route('frontend.getCountyGrid') }}";
        window.GEO_CHILDREN_URL  = "{{ route('frontend.geo.children') }}";
        window.COUNTY_SHOW_ROUTE = "{{ url('/county') }}/"; // county/{id}/{slug}
        window.CURRENT_GEO_ID    = {{ (int)($selectedGeo ?? 0) }};
    </script>
    <script src="{{ asset('public/frontend/pages/county.js') }}"></script>
@endpush
