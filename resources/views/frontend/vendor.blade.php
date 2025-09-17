@extends('layouts.frontend')

@section('title', $metadata['name'])
@php $gtext = gtext(); @endphp

@section('meta-content')
    <meta name="keywords" content="{{ $metadata['name'] }}" />
    <meta name="description" content="{{ $metadata['name'] }}" />
    <meta property="og:title" content="{{ $metadata['name'] }}" />
    <meta property="og:site_name" content="{{ $gtext['site_name'] }}" />
    <meta property="og:description" content="{{ $metadata['name'] }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ asset('public/media/'.($metadata['thumbnail'] ?? $gtext['og_image'])) }}" />
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
    <meta name="twitter:title" content="{{ $metadata['name'] }}">
    <meta name="twitter:description" content="{{ $metadata['name'] }}">
    <meta name="twitter:image" content="{{ asset('public/media/'.($metadata['thumbnail'] ?? $gtext['og_image'])) }}">
@endsection

@section('header')
    @include('frontend.partials.header')
@endsection

@section('content')

    <main class="main">
        <!-- Breadcrumb -->
        <div class="breadcrumb-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $metadata['name'] }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-title">
                            <h1>{{ $metadata['name'] }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                                <div class="col-6">
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
                                                <div class="col-6">
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

                                            {{-- Optional price controls matching your Brand IDs --}}
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <input type="number" id="filter_min_price" class="form-control form-control-sm" placeholder="{{ __('Min price') }}">
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-flex gap-2">
                                                        <input type="number" id="filter_max_price" class="form-control form-control-sm" placeholder="{{ __('Max price') }}">
                                                        <button id="FilterByPrice" class="btn btn-sm btn-primary">{{ __('Filter') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="vendor_id" value="{{ (int)($params['vendor_id'] ?? 0) }}">

                                        <div id="tp_datalist">
                                            @include('frontend.partials.vendor-grid')
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
    </main>

@endsection

@push('scripts')
    <script type="text/javascript">
        var vendor_id = "{{ (int)($params['vendor_id'] ?? 0) }}";
    </script>
    <script src="{{ asset('public/frontend/pages/vendor.js') }}"></script>
@endpush
