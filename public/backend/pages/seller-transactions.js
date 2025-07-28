"use strict";

// On Search Button Click
function onSearch() {
    let search = $('#search').val();
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();

    loadTransactions({
        search: search,
        start_date: start_date,
        end_date: end_date
    });
}

// On Filter Button Click
function onFilterAction() {
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();

    loadTransactions({
        start_date: start_date,
        end_date: end_date
    });
}

// General AJAX Loader
function loadTransactions(params = {}) {
    $.ajax({
        url: '/seller/transactions',
        type: 'GET',
        data: params,
        beforeSend: function () {
            $('#tp_datalist').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        },
        success: function (response) {
            $('#tp_datalist').html(response);
        },
        error: function (xhr) {
            let errorMsg = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Something went wrong while fetching transactions.';
            $('#tp_datalist').html('<div class="alert alert-danger text-center">' + errorMsg + '</div>');
        }
    });
}

// On Enter key in search input
$('#search').on('keypress', function (e) {
    if (e.which === 13) {
        onSearch();
    }
});
