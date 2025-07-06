var $ = jQuery.noConflict();

$(function () {
	"use strict";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

});

function onHomepageVariations(variation_value, variation_type) {
	$(".home_variation").text(Activate);
	$(".home_variation").removeClass("active");
	
	$("#home_page_variation").val(variation_value);
	
	onSaveVariations(variation_type);
}

function onCategoryPageVariation(variation_value, variation_type) {
	$(".category_page").text(Activate);
	$(".category_page").removeClass("active");
	$("#category_page_variation").val(variation_value);
	
	onSaveVariations(variation_type);
}

function onBrandPageVariation(variation_value, variation_type) {
	$(".brand_page").text(Activate);
	$(".brand_page").removeClass("active");
	$("#brand_page_variation").val(variation_value);
	
	onSaveVariations(variation_type);
}

function onSellerPageVariation(variation_value, variation_type) {
	$(".seller_page").text(Activate);
	$(".seller_page").removeClass("active");
	$("#seller_page_variation").val(variation_value);
	
	onSaveVariations(variation_type);
}

function onSaveVariations(variation_type) {
	
	var home_page_variation = $("#home_page_variation").val();
	var category_page_variation = $("#category_page_variation").val();
	var brand_page_variation = $("#brand_page_variation").val();
	var seller_page_variation = $("#seller_page_variation").val();
	
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/savePageVariation',
		data:{
			home_variation: home_page_variation,
			category_variation: category_page_variation,
			brand_variation: brand_page_variation,
			seller_variation: seller_page_variation
		},
		dataType: "json",
		success: function (response) {			
			var msgType = response.msgType;
			var msg = response.msg;

			if (msgType == "success") {
				onSuccessMsg(msg);
				
				if(variation_type == 'home'){
					$("#home_variation_"+home_page_variation).text(Activated);
					$("#home_variation_"+home_page_variation).addClass("active");
					
				}else if(variation_type == 'category'){
					$("#category_page_"+category_page_variation).text(Activated);
					$("#category_page_"+category_page_variation).addClass("active");
					
				}else if(variation_type == 'brand'){
					$("#brand_page_"+brand_page_variation).text(Activated);
					$("#brand_page_"+brand_page_variation).addClass("active");
					
				}else if(variation_type == 'seller'){
					$("#seller_page_"+seller_page_variation).text(Activated);
					$("#seller_page_"+seller_page_variation).addClass("active");
				}
				
			} else {
				onErrorMsg(msg);
			}
		}
	});
}