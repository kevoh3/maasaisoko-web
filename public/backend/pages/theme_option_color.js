var $ = jQuery.noConflict();

$(function () {
	"use strict";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$("#submit-form").on("click", function () {
        $("#DataEntry_formId").submit();
    });
	
	$('#theme_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#green_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#light_green_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#lightness_green_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#gray_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#dark_gray_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#light_gray_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#black_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
	$('#white_color_picker').colorpicker({
		format: 'hex' //format - hex | rgb | rgba.
	});
	
});

function showPerslyError() {
    $('.parsley-error-list').show();
}

jQuery('#DataEntry_formId').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) {
                return true;
            } else {
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
		url: base_url + '/backend/saveThemeOptionsColor',
		data: $('#DataEntry_formId').serialize(),
		success: function (response) {			
			var msgType = response.msgType;
			var msg = response.msg;
			if (msgType == "success") {
				onSuccessMsg(msg);
			} else {
				onErrorMsg(msg);
			}
		}
	});
}

