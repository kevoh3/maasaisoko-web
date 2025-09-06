// public/frontend/pages/view_cart.js
var $ = jQuery.noConflict();

$(function () {
    "use strict";

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    onViewCartData();
});

/** ---------- Totals (right column) ---------- */
function onViewCartData() {
    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/viewcart_data',
        dataType: 'json',
        success: function (data) {
            $(".viewcart_price_total").text(data.price_total);
            $(".viewcart_discount").text?.(data.discount); // in case exists
            $(".viewcart_tax").text(data.tax);
            $(".viewcart_sub_total").text(data.sub_total);
            $(".viewcart_total").text(data.total);
        }
    });
}

/** ---------- Helpers (line total & inputs) ---------- */
function _qtyInput(id) {
    // Find the qty <input> in this row
    return $('#row_delete_' + id + ' .pro-quantity-w input[type="number"]');
}

function _formatMoneyUsingRow(id, amount) {
    var $unit = $('#unit_price_' + id);
    var pos = $unit.data('cpos');     // 'left' | 'right'
    var icon = $unit.data('cicon');   // e.g., KSh
    var n = (Math.round((amount + Number.EPSILON) * 100) / 100)
        .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    return (pos === 'left') ? (icon + n) : (n + icon);
}

function _updateLineTotal(id) {
    var $unit = $('#unit_price_' + id);
    var unit = parseFloat($unit.data('unit')) || 0;
    var qty = parseInt(_qtyInput(id).val(), 10) || 1;
    var line = unit * qty;
    $('#line_total_' + id).text(_formatMoneyUsingRow(id, line));
}

/** ---------- Remove line ---------- */
function onRemoveToCart(id) {
    var rowid = $("#removetoviewcart_" + id).data('id');

    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/remove_to_cart/' + rowid,
        dataType: 'json',
        success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;

            if (msgType == "success") {
                onSuccessMsg(msg);
                $('#row_delete_' + id).remove();
            } else {
                onErrorMsg(msg);
            }

            onViewCartData();
            if (typeof onViewCart === 'function') onViewCart(); // mini-cart refresh if available
        }
    });
}

/** ---------- Quantity: -1 / +1 / set exact ---------- */
function onDecreaseQty(id) {
    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/decrease_to_cart/' + id,
        dataType: 'json',
        success: function (res) {
            if (res.msgType === 'success') {
                onSuccessMsg(res.msg);

                // Update UI without reload
                var $inp = _qtyInput(id);
                var newQty = (parseInt($inp.val(), 10) || 1) - 1;
                if (newQty < 1) {
                    $('#row_delete_' + id).remove();
                } else {
                    $inp.val(newQty);
                    _updateLineTotal(id);
                }
                onViewCartData();
                if (typeof onViewCart === 'function') onViewCart();
            } else {
                onErrorMsg(res.msg || 'Failed to decrease quantity.');
            }
        }
    });
}

function onIncreaseQty(id) {
    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/increase_to_cart/' + id,
        dataType: 'json',
        success: function (res) {
            if (res.msgType === 'success') {
                onSuccessMsg(res.msg);

                // Update UI without reload
                var $inp = _qtyInput(id);
                var newQty = (parseInt($inp.val(), 10) || 1) + 1;
                $inp.val(newQty);
                _updateLineTotal(id);

                onViewCartData();
                if (typeof onViewCart === 'function') onViewCart();
            } else {
                onErrorMsg(res.msg || 'Failed to increase quantity.');
            }
        }
    });
}

// Set exact quantity from an <input>
function onSetQty(id, qty) {
    qty = parseInt(qty, 10);
    if (!qty || qty < 1) {
        onErrorMsg(TEXT['Please enter quantity.']);
        _qtyInput(id).val(1);
        qty = 1;
    }
    $.ajax({
        type: 'GET',
        url: base_url + '/frontend/update_cart_qty/' + id + '/' + qty,
        dataType: 'json',
        success: function (res) {
            if (res.msgType === 'success') {
                onSuccessMsg(res.msg);
                _updateLineTotal(id);
                onViewCartData();
                if (typeof onViewCart === 'function') onViewCart();
            } else {
                onErrorMsg(res.msg || 'Failed to update quantity.');
            }
        }
    });
}

/** ---------- (Optional) No-inline handlers ----------
 * If you later remove inline onclick/onchange from Blade, uncomment below:
 *
 $(document).on('click', '.qty-minus', function(){
 onDecreaseQty($(this).data('id'));
 });
 $(document).on('click', '.qty-plus', function(){
 onIncreaseQty($(this).data('id'));
 });
 $(document).on('change', '.qty-input', function(){
 onSetQty($(this).data('id'), this.value);
 });
 */
