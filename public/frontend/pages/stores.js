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

	$(document).on('click', '.pagination a', function(event){
		event.preventDefault(); 
		var page = $(this).attr('href').split('page=')[1];
		onPaginationDataLoad(page);
	});
	
	$("#num").on("change", function(){
		num = $('#num').val();
		onRefreshData();
	});
	
	$("#sortby").on("change", function(){
		sortby = $('#sortby').val();
		onRefreshData();
	});
	
	$("#filter_min_price").val(0);
	$("#filter_max_price").val('');
	
	$("#FilterByPrice").on("click", function(){
        onRefreshData();
    });	
});

function onPaginationDataLoad(page){

	$.ajax({
		url:base_url + "/frontend/getStoresGrid",
		data:{page:page,num:num,sortby:sortby,seller_id:seller_id,min_price:min_price,max_price:max_price},
		success:function(data){
			$('#tp_datalist').html(data);
		}
	});
}

function onRefreshData(){
	min_price = $("#filter_min_price").val();
	max_price = $("#filter_max_price").val();
	
 	$.ajax({
		url:base_url + "/frontend/getStoresGrid",
		data:{num:num,sortby:sortby,seller_id:seller_id,min_price:min_price,max_price:max_price},
		success:function(data){
			$('#tp_datalist').html(data);
		}
	});
}


