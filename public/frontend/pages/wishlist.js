var $ = jQuery.noConflict();

$(function () {
    "use strict";
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
});

function wlQty(id){
    var v = parseInt($('#wl_qty_' + id).val(), 10);
    return (v && v > 0) ? v : 1;
}

function onRemoveToWishlist(id) {
    var rowid = $("#removetowishlist_"+id).data('id');

    $.ajax({
        type : 'GET',
        url: base_url + '/frontend/remove_to_wishlist/' + rowid,
        dataType:"json",
        success: function (response) {
            var msgType = response.msgType, msg = response.msg;
            if (msgType == "success") {
                onSuccessMsg(msg);
                $('#row_delete_'+id).remove();
                // if no more rows, show empty state
                if ($('#wishlist_rows').children().length === 0) {
                    location.reload();
                }
            } else {
                onErrorMsg(msg);
            }
            if (typeof onWishlist === 'function') onWishlist();
        }
    });
}

// Add to cart (keeps item in wishlist)
function onAddWishlistToCart(id) {
    var qty = wlQty(id);

    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/add_to_cart/' + id + '/' + qty,
        dataType: 'json',
        success: function (res) {
            if (res.msgType === 'success') {
                onSuccessMsg(res.msg);
                // refresh mini-cart if global helper exists
                if (typeof onViewCart === 'function') onViewCart();
            } else {
                onErrorMsg(res.msg || 'Failed to add to cart.');
            }
        }
    });
}

// Move to cart (adds & removes from wishlist)
function onMoveWishlistToCart(id) {
    var qty = wlQty(id);

    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/move_wishlist_to_cart/' + id + '/' + qty,
        dataType: 'json',
        success: function (res) {
            if (res.msgType === 'success') {
                onSuccessMsg(res.msg);
                $('#row_delete_'+id).remove();
                if (typeof onViewCart === 'function') onViewCart();
                if (typeof onWishlist === 'function') onWishlist();
                // if no more rows, show empty state
                if ($('#wishlist_rows').children().length === 0) {
                    location.reload();
                }
            } else {
                onErrorMsg(res.msg || 'Failed to move to cart.');
            }
        }
    });
}
