var $ = jQuery.noConflict();
var RecordId = '';
var BulkAction = '';
var ids = [];

$(function () {
	"use strict";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	resetForm("DataEntry_formId");
	resetForm("bankInformation_formId");

	$("#submit-form").on("click", function () {
        $("#DataEntry_formId").submit();
    });

	$("#bank_information_submit_form").on("click", function () {
        $("#bankInformation_formId").submit();
    });

	$(document).on('click', '.users_pagination nav ul.pagination a', function(event){
		event.preventDefault();
		var page = $(this).attr('href').split('page=')[1];
		onPaginationDataLoad(page);
	});

	$('input:checkbox').prop('checked',false);

    $(".checkAll").on("click", function () {
        $("input:checkbox").not(this).prop("checked", this.checked);
    });

	$("#status_id").chosen();
	$("#status_id").trigger("chosen:updated");

	$('.toggle-password').on('click', function() {
		$(this).toggleClass('fa-eye-slash');
			let input = $($(this).attr('toggle'));
		if (input.attr('type') == 'password') {
			input.attr('type', 'text');
		}else {
			input.attr('type', 'password');
		}
	});

	$("#on_thumbnail").on("click", function () {
		onGlobalMediaModalView();
    });

	$("#media_select_file").on("click", function () {
		var thumbnail = $("#thumbnail").val();

		if(thumbnail !=''){
			$("#photo_thumbnail").val(thumbnail);
			$("#view_photo_thumbnail").html('<img src="'+public_path+'/media/'+thumbnail+'">');
		}

		$("#remove_photo_thumbnail").show();
		$('#global_media_modal_view').modal('hide');
    });

	$("#view_by_status").val(0);

});

function onCheckAll() {
    $(".checkAll").on("click", function () {
        $("input:checkbox").not(this).prop("checked", this.checked);
    });
}

function onPaginationDataLoad(page) {
	$.ajax({
		url:base_url + "/backend/getSellersTableData?page="+page+"&search="+$("#search").val()+"&status="+$("#view_by_status").val(),
		success:function(data){
			$('#tp_datalist').html(data);
			onCheckAll();
		}
	});
}

function onRefreshData() {
	$.ajax({
		url:base_url + "/backend/getSellersTableData?search="+$("#search").val()+"&status="+$("#view_by_status").val(),
		success:function(data){
			$('#tp_datalist').html(data);
			onCheckAll();
		}
	});
}

function onSearch() {

	$.ajax({
		url: base_url + "/backend/getSellersTableData?search="+$("#search").val()+"&status="+$("#view_by_status").val(),
		success:function(data){
			$('#tp_datalist').html(data);
			onCheckAll();
		}
	});
}

function onDataViewByStatus(status) {

	$("#view_by_status").val(status);

	$(".orderstatus").removeClass('active')
	$("#orderstatus_"+status).addClass('active');

	$.ajax({
		url: base_url + "/backend/getSellersTableData?status="+$("#view_by_status").val()+"&search="+$("#search").val(),
		success:function(data){
			$('#tp_datalist').html(data);
			onCheckAll();
		}
	});
}

function resetForm(id) {
    $('#' + id).each(function () {
        this.reset();
    });

	$("#status_id").trigger("chosen:updated");
}

function onListPanel() {
	$('.parsley-error-list').hide();
    $('#list-panel, .btn-form').show();
    $('#form-panel, .btn-list').hide();
}

function onFormPanel() {
	var passtype = $('#password').attr('type');
	if(passtype == 'text'){
		$(".toggle-password").removeClass("fa-eye-slash");
		$(".toggle-password").addClass("fa-eye");
		$('#password').attr('type', 'password');
	}

    resetForm("DataEntry_formId");
    resetForm("bankInformation_formId");
	RecordId = '';

	$("#status_id").trigger("chosen:updated");

	$("#remove_photo_thumbnail").hide();
	$("#photo_thumbnail").html('');

    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();

	onDetailsBankInfo(1);
	$("#details_bank_info_2").hide();

	$(".error_available").html('');
}

function onEditPanel() {
    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();

	$("#details_bank_info_2").show();
	$(".error_available").html('');
}

function onMediaImageRemove(type) {
	$('#photo_thumbnail').val('');
	$("#remove_photo_thumbnail").hide();
}

function onDetailsBankInfo(id) {
	if(id == 1){
		$("#bank_information").hide();
		$('#details').show();
		$(".details_bank_info").removeClass("active");
		$("#details_bank_info_1").addClass("active");
	}else{
		$('#details').hide();
		$("#bank_information").show();
		$(".details_bank_info").removeClass("active");
		$("#details_bank_info_2").addClass("active");
	}
}
function onDetailsTab(idToShow){
    // buttons
    $('.details_tab').removeClass('active');
    if(idToShow === 'details')          $('#tab_details').addClass('active');
    else if(idToShow === 'bank_information') $('#tab_bank').addClass('active');
    else if(idToShow === 'package_info') $('#tab_package').addClass('active');
    else if(idToShow === 'kyc_info')     $('#tab_kyc').addClass('active');

    // panels
    $('#details, #bank_information, #package_info, #kyc_info').addClass('dnone');
    $('#' + idToShow).removeClass('dnone');
}
function showPerslyError() {
    $('.parsley-error-list').show();
}

jQuery('#DataEntry_formId').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) {
                return true;
            }
            else {
                showPerslyError();
                return false;
            }
        },
        onFormSubmit: function (isFormValid, event) {
            if (isFormValid) {
                onConfirmWhenAddEdit();
                return false;
            }
        }
    }
});

function onConfirmWhenAddEdit() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/saveSellersData',
		data: $('#DataEntry_formId').serialize(),
		success: function (response) {
			var msgType = response.msgType;
			var msg = response.msg;
			var id = response.id;
			$("#RecordId").val(id);
			$("#seller_id").val(id);
			if (msgType == "success") {

				if(RecordId == ''){
					RecordId = id;
					$('#details').hide();
					$("#bank_information").show();
					$("#details_bank_info_2").show();
					$(".details_bank_info").removeClass("active");
					$("#details_bank_info_2").addClass("active");
				}

				onRefreshData();
				onSuccessMsg(msg);

			} else {
				onErrorMsg(msg);
			}

			onCheckAll();
		}
	});
}

jQuery('#bankInformation_formId').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) {
                return true;
            }
            else {
                showPerslyError();
                return false;
            }
        },
        onFormSubmit: function (isFormValid, event) {
            if (isFormValid) {
                onBankInformationAddEdit();
                return false;
            }
        }
    }
});

function onBankInformationAddEdit() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/saveBankInformationData',
		data: $('#bankInformation_formId').serialize(),
		success: function (response) {
			var msgType = response.msgType;
			var msg = response.msg;
			var id = response.id;
			$("#bank_information_id").val(id);
			if (msgType == "success") {
				onRefreshData();
				onSuccessMsg(msg);
			} else {
				onErrorMsg(msg);
			}

			onCheckAll();
		}
	});
}

function onEdit(id) {
	RecordId = id;
	var msg = TEXT["Do you really want to view this record"];
	onCustomModal(msg, "onLoadEditData");
}

function onLoadEditData() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/getSellerById',
		data: 'id='+RecordId,
		success: function (response) {

			var seller_data = response.seller_data;
			var bank_info_data = response.bank_information;

			var passtype = $('#password').attr('type');
			if(passtype == 'text'){
				$(".toggle-password").removeClass("fa-eye-slash");
				$(".toggle-password").addClass("fa-eye");
				$('#password').attr('type', 'password');
			}
			$("#seller_id").val(seller_data.id);
			$("#RecordId").val(seller_data.id);
			$("#name").val(seller_data.name);
			$("#email").val(seller_data.email);
			$("#password").val(seller_data.bactive);
			$("#phone").val(seller_data.phone);
			$("#shop_name").val(seller_data.shop_name);
			$("#shop_url").val(seller_data.shop_url);
			$("#shopurl").text(seller_data.shop_url);
			$("#shop_url_id").text(seller_data.id);

			$("#address").val(seller_data.address);
			$("#city").val(seller_data.city);
			$("#state").val(seller_data.state);
			$("#zip_code").val(seller_data.zip_code);
			$("#country_id").val(seller_data.country_id).trigger("chosen:updated");
			$("#status_id").val(seller_data.status_id).trigger("chosen:updated");

			if(seller_data.photo != null){
				$("#photo_thumbnail").val(seller_data.photo);
				$("#view_photo_thumbnail").html('<img src="'+public_path+'/media/'+seller_data.photo+'">');
				$("#remove_photo_thumbnail").show();
			}else{
				$("#photo_thumbnail").val('');
				$("#view_photo_thumbnail").html('');
				$("#remove_photo_thumbnail").hide();
			}

			if(seller_data.status_id == 1){
				$("#seller_status").removeClass("inactive").addClass("active");
				$("#seller_status").text(TEXT['Active']);
			}else{
				$("#seller_status").removeClass("active").addClass("inactive");
				$("#seller_status").text(TEXT['Inactive']);
			}

			if(bank_info_data != null){
				$("#bank_name").val(bank_info_data.bank_name);
				$("#bank_code").val(bank_info_data.bank_code);
				$("#account_number").val(bank_info_data.account_number);
				$("#account_holder").val(bank_info_data.account_holder);
				$("#paypal_id").val(bank_info_data.paypal_id);
				$("#description").val(bank_info_data.description);
				$("#bank_information_id").val(bank_info_data.id);
			}else{
				$("#bank_name").val('');
				$("#bank_code").val('');
				$("#account_number").val('');
				$("#account_holder").val('');
				$("#paypal_id").val('');
				$("#description").val('');
				$("#bank_information_id").val('');
			}

			$("#created_at").text(seller_data.created_at);
			// $("#Current_Balance").text(response.CurrentBalance);
			$("#OrderBalance").text(response.OrderBalance);
			$("#WithdrawalBalance").text(response.WithdrawalBalance);
			$("#TotalProducts").text(response.TotalProducts);
            var pkg = response.package || null;
            if (pkg) {
                $('#pkg_name').text(pkg.name || '—');
                $('#pkg_status').text(pkg.status || '—')
                    .removeClass('badge-success badge-warning badge-secondary badge-danger')
                    .addClass((pkg.status === 'active') ? 'badge-success'
                        : (pkg.status === 'canceled') ? 'badge-secondary'
                            : (pkg.status === 'expired') ? 'badge-danger'
                                : 'badge-warning');
                $('#pkg_billing_cycle').text(pkg.billing_cycle || '—');
                $('#pkg_price').text(pkg.price || '—');         // already formatted server-side
                $('#pkg_currency').text(pkg.currency || '—');
                $('#pkg_starts_at').text(pkg.starts_at || '—');
                $('#pkg_expires_at').text(pkg.expires_at || '—');
            } else {
                $('#pkg_name, #pkg_billing_cycle, #pkg_price, #pkg_currency, #pkg_starts_at, #pkg_expires_at').text('—');
                $('#pkg_status').text('{{ __("No Active Subscription") }}')
                    .removeClass('badge-success badge-warning badge-secondary badge-danger')
                    .addClass('badge-secondary');
            }

// ----- KYC (from response.seller_data fields already returned) -----
            var kycStatus = (seller_data.kyc_status || 'not_submitted');
            $('#kyc_status_badge')
                .text(kycStatus.replace('_', ' '))
                .removeClass('badge-success badge-warning badge-secondary badge-danger')
                .addClass(
                    kycStatus === 'verified' ? 'badge-success' :
                        kycStatus === 'pending'  ? 'badge-warning' :
                            kycStatus === 'rejected' ? 'badge-danger'  : 'badge-secondary'
                );

            $('#kyc_document_number').text(seller_data.document_number || '—');
            $('#kyc_submitted_at').text(seller_data.kyc_submitted_at || '—');
            $('#kyc_verified_at').text(seller_data.kyc_verified_at || '—');
            $('#kyc_rejected_at').text(seller_data.kyc_rejected_at || '—');
            $('#kyc_notes').text(seller_data.kyc_notes || '—');
            // ----- WALLETS -----
            function maskAcct(num){
                if(!num) return '—';
                // keep last 4 visible
                var last4 = num.slice(-4);
                return '•••• ' + last4;
            }

            var wallets = response.wallets || [];
            if (wallets.length === 0) {
                $('#primary_wallet').addClass('dnone');
                $('#other_wallets').addClass('dnone');
                $('#no_wallet').removeClass('dnone');
            } else {
                $('#no_wallet').addClass('dnone');

                // Primary = first element (sorted server-side)
                var pw = wallets[0];
                $('#Wallet_Name').text(pw.wallet_name || '—');
                $('#Wallet_Balance').text(pw.balance || '—');
                $('#Wallet_Account').text(maskAcct(pw.account_number));
                $('#Wallet_Type').text(pw.wallet_type || '—');
                $('#Wallet_Currency').text(pw.currency || '—');
                $('#Wallet_Limit').text(pw.wallet_limit || '—');
                $('#primary_wallet').removeClass('dnone');

                // Others
                if (wallets.length > 1) {
                    var html = '';
                    for (var i = 1; i < wallets.length; i++) {
                        var w = wallets[i];
                        html += '<div class="mb-10 p-10 rounded" style="background:#f8f9fa;">'
                            +   '<div class="d-flex justify-content-between align-items-center">'
                            +     '<div>'
                            +       '<div class="font-bold">'+ (w.wallet_name || "—") +'</div>'
                            +       '<div class="small text-muted">'
                            +         '{{ __("Account") }}: '+ maskAcct(w.account_number)
                            +         ' &nbsp; • &nbsp; {{ __("Type") }}: '+ (w.wallet_type || "—")
                            +         ' &nbsp; • &nbsp; {{ __("Curr.") }}: '+ (w.currency || "—")
                            +       '</div>'
                            +     '</div>'
                            +     '<div class="font-bold">'+ (w.balance || "—") +'</div>'
                            +   '</div>'
                            + '</div>';
                    }
                    $('#wallets_container').html(html);
                    $('#other_wallets').removeClass('dnone');
                } else {
                    $('#wallets_container').empty();
                    $('#other_wallets').addClass('dnone');
                }
            }

			onEditPanel();
		}
    });
}

function onDelete(id) {
	RecordId = id;
	var msg = TEXT["Do you really want to Suspend this record"];
	onCustomModal(msg, "onConfirmDelete");
}

function onConfirmDelete() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/deleteSeller',
		data: 'id='+RecordId,
		success: function (response) {
			var msgType = response.msgType;
			var msg = response.msg;

			if(msgType == "success"){
				onSuccessMsg(msg);
				onRefreshData();
			}else{
				onErrorMsg(msg);
			}

			onCheckAll();
		}
    });
}

function onBulkAction() {
	ids = [];
	$('.selected_item:checked').each(function(){
		ids.push($(this).val());
	});

	if(ids.length == 0){
		var msg = TEXT["Please select record"];
		onErrorMsg(msg);
		return;
	}

	BulkAction = $("#bulk-action").val();
	if(BulkAction == ''){
		var msg = TEXT["Please select action"];
		onErrorMsg(msg);
		return;
	}

	if(BulkAction == 'active'){
		var msg = TEXT["Do you really want to active this records"];
	}else if(BulkAction == 'inactive'){
		var msg = TEXT["Do you really want to inactive this records"];
	}else if(BulkAction == 'delete'){
		var msg = TEXT["Do you really want to delete this records"];
	}

	onCustomModal(msg, "onConfirmBulkAction");
}

function onConfirmBulkAction() {

    $.ajax({
		type : 'POST',
		url: base_url + '/backend/bulkActionSellers',
		data: 'ids='+ids+'&BulkAction='+BulkAction,
		success: function (response) {
			var msgType = response.msgType;
			var msg = response.msg;

			if(msgType == "success"){
				onSuccessMsg(msg);
				onRefreshData();
				ids = [];
			}else{
				onErrorMsg(msg);
			}

			onCheckAll();
		}
    });
}

$("#shop_url").on("blur", function () {
	var shop_url = $("#shop_url").val();
	var str_name = shop_url.trim();
	var strLength = str_name.length;
	if(strLength>0){
		$.ajax({
			type : 'POST',
			url: base_url + '/frontend/hasShopSlug',
			data: 'shop_url='+shop_url,
			success: function (response) {
				var slug = response.slug;
				$("#shop_url").val(slug);
			}
		});
	}
});
