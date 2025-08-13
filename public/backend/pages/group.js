var $ = jQuery.noConflict();
var RecordId = '';
var BulkAction = '';
var ids = [];

$(function () {
    "use strict";

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Init
    resetForm("GroupEntry_formId");
    $('input:checkbox').prop('checked', false);
    $("#view_by_status").val(0);

    // Chosen on group selects
    $("#status").chosen();           $("#status").trigger("chosen:updated");
    $("#verified_status").chosen();  $("#verified_status").trigger("chosen:updated");
    $("#type").chosen();             $("#type").trigger("chosen:updated");

    // Submit button -> submit the Group form
    $("#submit-form").on("click", function () {
        $("#GroupEntry_formId").submit();
    });

    // Pagination (AJAX)
    $(document).on('click', '.users_pagination nav ul.pagination a', function(e){
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        onPaginationDataLoad(page);
    });

    // Check all
    $(".checkAll").on("click", function () {
        $("input:checkbox").not(this).prop("checked", this.checked);
    });

    // Global media picker
    $("#on_thumbnail").on("click", function () {
        onGlobalMediaModalView();
    });

    $("#media_select_file").on("click", function () {
        var thumbnail = $("#thumbnail").val(); // set by global-media.js
        if (thumbnail) {
            $("#logo_thumbnail").val(thumbnail);
            $("#view_photo_thumbnail").html('<img src="'+ public_path +'/media/'+ thumbnail +'">');
            $("#remove_photo_thumbnail").show();
            $('#global_media_modal_view').modal('hide');
        }
    });
});

// Helpers
function onCheckAll() {
    $(".checkAll").on("click", function () {
        $("input:checkbox").not(this).prop("checked", this.checked);
    });
}

function onPaginationDataLoad(page) {
    $.ajax({
        url: base_url + "/backend/getGroupsTableData?page="+page+"&search="+$("#search").val()+"&status="+$("#view_by_status").val(),
        success: function (data) {
            $('#tp_datalist').html(data);
            onCheckAll();
        }
    });
}

function onRefreshData() {
    $.ajax({
        url: base_url + "/backend/getGroupsTableData?search="+$("#search").val()+"&status="+$("#view_by_status").val(),
        success: function (data) {
            $('#tp_datalist').html(data);
            onCheckAll();
        }
    });
}

function onSearch() {
    $.ajax({
        url: base_url + "/backend/getGroupsTableData?search="+$("#search").val()+"&status="+$("#view_by_status").val(),
        success: function (data) {
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
        url: base_url + "/backend/getGroupsTableData?status="+$("#view_by_status").val()+"&search="+$("#search").val(),
        success: function (data) {
            $('#tp_datalist').html(data);
            onCheckAll();
        }
    });
}

function resetForm(id) {
    $('#' + id).each(function () { this.reset(); });
    $("#status").trigger("chosen:updated");
    $("#verified_status").trigger("chosen:updated");
    $("#type").trigger("chosen:updated");

    $("#logo_thumbnail").val('');
    $("#view_photo_thumbnail").html('');
    $("#remove_photo_thumbnail").hide();
}

function onListPanel() {
    $('.parsley-error-list').hide();
    $('#list-panel, .btn-form').show();
    $('#form-panel, .btn-list').hide();
}

function onFormPanel() {
    resetForm("GroupEntry_formId");
    RecordId = '';
    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();
    $(".error_available").html('');
}

function onEditPanel() {
    $('#list-panel, .btn-form').hide();
    $('#form-panel, .btn-list').show();
    $(".error_available").html('');
}

function onMediaImageRemove() {
    $('#logo_thumbnail').val('');
    $("#remove_photo_thumbnail").hide();
    $("#view_photo_thumbnail").html('');
}

// Parsley
function showPerslyError() { $('.parsley-error-list').show(); }

jQuery('#GroupEntry_formId').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) { return true; }
            showPerslyError(); return false;
        },
        onFormSubmit: function (isFormValid, event) {
            if (isFormValid) { onConfirmWhenAddEdit(); return false; }
        }
    }
});

function onConfirmWhenAddEdit() {
    $.ajax({
        type: 'POST',
        url: base_url + '/backend/saveGroupData',
        data: $('#GroupEntry_formId').serialize(),
        success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;
            var id = response.id;
            $("#RecordId").val(id);

            if (msgType === "success") {
                if (!RecordId) { RecordId = id; }
                onRefreshData();
                onSuccessMsg(msg);
                onListPanel();
            } else {
                onErrorMsg(msg);
            }
            onCheckAll();
        },
        error: function(xhr){
            onErrorMsg(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save group.');
        }
    });
}

// Edit
function onEdit(id) {
    RecordId = id;
    var msg = TEXT["Do you really want to edit this record"];
    onCustomModal(msg, "onLoadEditData");
}

function onLoadEditData() {
    $.ajax({
        type: 'POST',
        url: base_url + '/backend/getGroupById',
        data: 'id=' + RecordId,
        success: function (response) {
            var g = response.group;

            $("#RecordId").val(g.id);
            $("#name").val(g.name);
            $("#registration_number").val(g.registration_number);
            $("#type").val(g.type).trigger("chosen:updated");
            $("#industry").val(g.industry);

            $("#contact_person").val(g.contact_person);
            $("#phone").val(g.phone);
            $("#email").val(g.email);
            $("#address").val(g.address);
            $("#county").val(g.county);
            $("#sub_county").val(g.sub_county);

            $("#kra_pin").val(g.kra_pin);
            $("#business_permit_number").val(g.business_permit_number);
            $("#certificate_of_incorporation").val(g.certificate_of_incorporation);
            $("#tax_compliance_certificate").val(g.tax_compliance_certificate);

            $("#bank_name").val(g.bank_name);
            $("#bank_branch").val(g.bank_branch);
            $("#bank_account_number").val(g.bank_account_number);

            $("#website").val(g.website);
            $("#social_media").val(g.social_media);

            $("#status").val(g.status).trigger("chosen:updated");
            $("#verified_status").val(g.verified_status).trigger("chosen:updated");
            $("#verified_notes").val(g.verified_notes || '');

            if (g.photo) {
                $("#logo_thumbnail").val(g.photo);
                $("#view_photo_thumbnail").html('<img src="'+ public_path +'/media/'+ g.photo +'">');
                $("#remove_photo_thumbnail").show();
            } else {
                $("#logo_thumbnail").val('');
                $("#view_photo_thumbnail").html('');
                $("#remove_photo_thumbnail").hide();
            }

            onEditPanel();
        }
    });
}

// Delete
function onDelete(id) {
    RecordId = id;
    var msg = TEXT["Do you really want to delete this record"];
    onCustomModal(msg, "onConfirmDelete");
}

function onConfirmDelete() {
    $.ajax({
        type: 'POST',
        url: base_url + '/backend/deleteGroup',
        data: 'id=' + RecordId,
        success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;

            if (msgType === "success") {
                onSuccessMsg(msg);
                onRefreshData();
                onListPanel();
            } else {
                onErrorMsg(msg);
            }
            onCheckAll();
        }
    });
}

// Bulk
function onBulkAction() {
    ids = [];
    $('.selected_item:checked').each(function(){ ids.push($(this).val()); });

    if (ids.length === 0) { return onErrorMsg(TEXT["Please select record"]); }

    BulkAction = $("#bulk-action").val();
    if (BulkAction === '') { return onErrorMsg(TEXT["Please select action"]); }

    var msg = TEXT["Do you really want to " + (BulkAction === 'delete' ? 'delete this records' : (BulkAction === 'active' ? 'active this records' : 'inactive this records'))];
    onCustomModal(msg, "onConfirmBulkAction");
}

function onConfirmBulkAction() {
    $.ajax({
        type: 'POST',
        url: base_url + '/backend/bulkActionGroups',
        data: 'ids=' + ids + '&BulkAction=' + BulkAction,
        success: function (response) {
            var msgType = response.msgType;
            var msg = response.msg;

            if (msgType === "success") {
                onSuccessMsg(msg);
                onRefreshData();
                ids = [];
            } else {
                onErrorMsg(msg);
            }
            onCheckAll();
        }
    });
}
