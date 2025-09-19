// public/frontend/pages/county.js
var $ = jQuery.noConflict();
var num = '';
var sortby = '';
var min_price = '';
var max_price = '';

$(function () {
    "use strict";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Resolve grid URL (prefer blade-provided constant; fallback to base_url)
    var GRID_URL = (typeof window.COUNTY_GRID_URL !== 'undefined' && window.COUNTY_GRID_URL)
        ? window.COUNTY_GRID_URL
        : (typeof base_url !== 'undefined' ? (base_url + "/frontend/getCountyGrid") : "/frontend/getCountyGrid");

    var $list     = $('#tp_datalist');
    var $geo      = $('#geo'); // hidden input set in county.blade
    var $num      = $('#num');
    var $sortby   = $('#sortby');

    // Price inputs (support both sets of IDs)
    var $minA = $('#min_price');
    var $maxA = $('#max_price');
    var $minB = $('#filter_min_price');
    var $maxB = $('#filter_max_price');
    var $btnB = $('#FilterByPrice');

    // defaults
    if ($minB.length) $minB.val(0);
    if ($maxB.length) $maxB.val('');

    // pagination (delegate inside the container)
    $(document).on('click', '#tp_datalist .pagination a', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (!href) return;

        var page = (href.indexOf('page=') >= 0) ? href.split('page=')[1] : '';
        onPaginationDataLoad(GRID_URL, page);
    });

    $num.on('change', function () {
        num = $num.val();
        onRefreshData(GRID_URL);
    });

    $sortby.on('change', function () {
        sortby = $sortby.val();
        onRefreshData(GRID_URL);
    });

    // live change on A inputs
    $minA.on('change', function () { onRefreshData(GRID_URL); });
    $maxA.on('change', function () { onRefreshData(GRID_URL); });

    // button click for B inputs (brand-style)
    $btnB.on('click', function () { onRefreshData(GRID_URL); });

    // helpers
    function readPrices() {
        // prefer A ids if present, else fall back to B ids
        var min = $minA.length ? $minA.val() : ($minB.length ? $minB.val() : '');
        var max = $maxA.length ? $maxA.val() : ($maxB.length ? $maxB.val() : '');
        return { min: min, max: max };
    }

    function paramsBase() {
        var prices = readPrices();
        return {
            geo: $geo.val() || 0,
            num: num || $num.val() || '',
            sortby: (sortby || $sortby.val() || ''),
            min_price: prices.min,
            max_price: prices.max
        };
    }

    // ajax calls
    window.onPaginationDataLoad = function (url, page) {
        var data = paramsBase();
        if (page) data.page = page;

        $.ajax({
            url: url,
            data: data,
            beforeSend: function(){ $list.addClass('opacity-50'); },
            complete: function(){ $list.removeClass('opacity-50'); },
            success: function (html) { $list.html(html); },
            error: function () {
                $list.html('<div class="alert alert-danger">{{ __("Failed to load products.") }}</div>');
            }
        });
    };

    window.onRefreshData = function (url) {
        var data = paramsBase();

        $.ajax({
            url: url,
            data: data,
            beforeSend: function(){ $list.addClass('opacity-50'); },
            complete: function(){ $list.removeClass('opacity-50'); },
            success: function (html) { $list.html(html); },
            error: function () {
                $list.html('<div class="alert alert-danger">{{ __("Failed to load products.") }}</div>');
            }
        });
    };
});
