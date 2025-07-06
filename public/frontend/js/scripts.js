
(function ($) {
	'use strict';

	//Preloader
	var win = $(window);
	win.on('load',function() {
		$('.tw-loader').delay(100).fadeOut('slow');
	});
	
	//Menu active
	var href = location.href;
	var elem = '.main-menu li a[href="' + href + '"]';
	
	$('ul.main-menu li').parent().removeClass('active');
	$('ul.main-menu li a').parent().removeClass('active');

	var parentClase = $(elem).parents();
	if (parentClase.length) {
		$(parentClase).addClass('active');
		$(elem).parent().addClass('active');
	}else{
		$(elem).addClass('active');
	}
	
	//ScrollToTop
	$(".scroll-to-top").scrollToTop(1000);
	
	//Header sticky
	win.on('scroll',function() {
		if ($(this).scrollTop() > 100){
			$('#sticky-header').addClass("sticky");
			$('#sticky-menu').addClass("sticky");
		}else{
			$('#sticky-header').removeClass("sticky");
			$('#sticky-menu').removeClass("sticky");
		}
	});
	
	//Category List
 	$('.navCategoryListActive').on('click', function () {
		$('.nav-category-list, .navCategoryListActive').toggleClass('open');
	});
	
	//Category More btn
 	$('.catMoreBtnActive').on('click', function () {
		$('.cat-list-hideshow, .onCatMoreBtn').toggleClass('open');
	});
	
	//Header Shoping Cart
 	$('.CartShowHide').on('click', function () {
		$('.headerShopingCart').toggleClass('open');
	});
	
	// Off Canvas Open close start
	$(".off-canvas-btn").on('click', function () {
		$(".mobile-menu-wrapper").addClass('open');
	});

	$(".offcanvas-btn-close, .off-canvas-overlay").on('click', function () {
		$(".mobile-menu-wrapper").removeClass('open');
	});

	// slide effect dropdown
	function dropdownAnimation() {
		$('.dropdown').on('show.bs.dropdown', function (e) {
			$(this).find('.dropdown-menu').first().stop(true, true).slideDown(500);
		});

		$('.dropdown').on('hide.bs.dropdown', function (e) {
			$(this).find('.dropdown-menu').first().stop(true, true).slideUp(500);
		});
	}
	
	dropdownAnimation();

	//offcanvas mobile menu start 
    var $offCanvasNav = $('.mobile-menu'),
        $offCanvasNavSubMenu = $offCanvasNav.find('.dropdown');
    
    //Add Toggle Button With Off Canvas Sub Menu
    $offCanvasNavSubMenu.parent().prepend('<span class="menu-expand"><i></i></span>');
    
    //Close Off Canvas Sub Menu
    $offCanvasNavSubMenu.slideUp();
    
    //Category Sub Menu Toggle
    $offCanvasNav.on('click', 'li a, li .menu-expand', function(e) {
        var $this = $(this);
        if ( ($this.parent().attr('class').match(/\b(has-children-menu|has-children|has-sub-menu)\b/)) && ($this.attr('href') === '#' || $this.hasClass('menu-expand')) ) {
            e.preventDefault();
            if ($this.siblings('ul:visible').length){
                $this.parent('li').removeClass('active');
                $this.siblings('ul').slideUp();
            } else {
                $this.parent('li').addClass('active');
                $this.closest('li').siblings('li').removeClass('active').find('li').removeClass('active');
                $this.closest('li').siblings('li').find('ul:visible').slideUp();
                $this.siblings('ul').slideDown();
            }
        }
    });
	
	//home-slider
	$('.home-slider').owlCarousel({
        navText: ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
		rtl: isRTL,
        loop: true,
        nav: true,
		dots: false,
		autoplay: false,
        mouseDrag: true,
		responsiveClass:true,
		smartSpeed: 1000,
		responsive:{
			0:{
				items:1
			},
			400:{
				items:1
			},
			600:{
				items:1
			},
			900:{
				items:1
			},
			1000:{
				items:1
			}
		}
	});
	
	//Featured Categories
	$('.featured-categories').owlCarousel({
        navText: ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
		rtl: isRTL,
        loop: true,
        nav: true,
		dots: false,
		margin: 15,
        mouseDrag: true,
		responsiveClass:true,
		smartSpeed: 1000,
		responsive:{
			0:{
				items:1,
				nav: false
			},
			400:{
				items:1,
				nav: false
			},
			600:{
				items:2,
				nav: false
			},
			900:{
				items:3
			},
			1000:{
				items:5
			}
		}
	});

	//Brands Carousel
	$(".brands-carousel").owlCarousel({
	navText: [
	  '<i class="bi bi-arrow-left"></i>',
	  '<i class="bi bi-arrow-right"></i>',
	],
	rtl: isRTL,
	loop: true,
	nav: true,
	dots: false,
	margin: 15,
	mouseDrag: true,
	responsiveClass: true,
	smartSpeed: 1000,
	responsive: {
			0: {
				items: 1,
				nav: false
			},
			400: {
				items: 2,
				nav: false
			},
			600: {
				items: 3
			},
			900: {
				items: 3
			},
			1000: {
				items: 6
			}
		}
	});
  
	//Categories
	$('.category-carousel').owlCarousel({
        navText: ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
		rtl: isRTL,
        loop: true,
        nav: true,
		dots: false,
		margin: 15,
        mouseDrag: true,
		responsiveClass:true,
		smartSpeed: 1000,
		responsive:{
			0:{
				items:1,
				nav: false
			},
			400:{
				items:1,
				nav: false
			},
			600:{
				items:2
			},
			900:{
				items:3
			},
			1000:{
				items:4
			}
		}
	});
	
	//Deals
	$('.deals-carousel').owlCarousel({
        navText: ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
		rtl: isRTL,
        loop: true,
        nav: true,
		dots: false,
		margin: 15,
        mouseDrag: true,
		responsiveClass:true,
		smartSpeed: 1000,
		responsive:{
			0:{
				items:1,
				nav: false
			},
			400:{
				items:1,
				nav: false
			},
			600:{
				items:2
			},
			1000:{
				items:3
			}
		}
	});
	
	//Deals Box
	$('.deals-carousel-box').owlCarousel({
        navText: ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
		rtl: isRTL,
        loop: true,
        nav: true,
		dots: false,
		margin: 15,
        mouseDrag: true,
		responsiveClass:true,
		smartSpeed: 1000,
		responsive:{
			0:{
				items:1,
				nav: false
			},
			400:{
				items:1,
				nav: false
			},
			600:{
				items:2
			},
			1000:{
				items:4
			}
		}
	});

	//Deals Of The Day
	$("[data-countdown]").each(function () {
		var countdownDate = $(this).data("countdown");
		$(this).countdown(countdownDate, function(event) {
		  var $this = $(this).html(event.strftime(''
			+ '<span class="countdown-section"><span class="countdown-amount">%D</span><span class="countdown-period">days</span></span>'
			+ '<span class="countdown-section"><span class="countdown-amount">%H</span><span class="countdown-period">hours</span></span>'
			+ '<span class="countdown-section"><span class="countdown-amount">%M</span><span class="countdown-period">mins</span></span>'
			+ '<span class="countdown-section"><span class="countdown-amount">%S</span><span class="countdown-period">sec</span></span>'));
		});
	});
	
	$('.popup-video').magnificPopup({
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false,
		disableOn: 300
	});
	
	//Price Range
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 1000,
      values: [0, 1000],
      slide: function( event, ui ) {
		var minPrice = ui.values[0];
		var maxPrice = ui.values[1];
		$("#filter_min_price").val(minPrice);
		$("#filter_max_price").val(maxPrice);
        $( "#amount" ).text( "$" + minPrice + " - $" + maxPrice);
      }
    });
	var minPrice = $( "#slider-range" ).slider( "values", 0);
	var maxPrice = $( "#slider-range" ).slider( "values", 1);
	$( "#amount" ).text("$" +minPrice+ " - $" + maxPrice);
	
	//Product Setails Slider
	$('.pd-slider-for').slick({
		rtl: isRTL,
		slidesToShow: 1,
		slidesToScroll: 1,
		draggable: false,
		speed: 300,
		asNavFor: '.pd-slider-nav',
		arrows: false,
		prevArrow: '<button type="button" class="slick-prev"><i class="bi bi-arrow-left"></i></button>',
		nextArrow: '<button type="button" class="slick-next"><i class="bi bi-arrow-right"></i></button>'
	});
	
	$('.pd-slider-nav').slick({
		rtl: isRTL,
		slidesToShow: 6,
		slidesToScroll: 1,
		asNavFor: '.pd-slider-for',
		dots: false,
		centerMode: false,
		focusOnSelect: true,
		draggable: false,
		arrows: true,
		prevArrow: '<button type="button" class="slick-prev"><i class="bi bi-arrow-left"></i></button>',
		nextArrow: '<button type="button" class="slick-next"><i class="bi bi-arrow-right"></i></button>',
		responsive: [{
		  breakpoint: 480,
		  settings: {
			slidesToShow: 4,
			slidesToScroll: 1,
			dots: false,
			arrows: false
		  }
		}
		]
	});
	
	//Subscribe for footer
	$(document).on("click", ".subscribe_btn", function(event) {
		event.preventDefault();

		var sub_email = $("#subscribe_email").val();
		var status = 'subscribed';
		
		var sub_btn = $('.sub_btn').html();
		var sub_recordid = '';
		
		var subscribe_email = sub_email.trim();
		
		if(subscribe_email == ''){
			$('.subscribe_msg').html('<p class="text-danger">The email address field is required.</p>');
			return;
		}
		
		$.ajax({
			type : 'POST',
			url: base_url + '/frontend/saveSubscriber',
			data: 'RecordId=' + sub_recordid+'&email_address='+subscribe_email+'&status='+status,
			beforeSend: function() {
				$('.subscribe_msg').html('');
				$('.sub_btn').html('<span class="spinner-border spinner-border-sm"></span> Please Wait...');
			},
			success: function (response) {			
				var msgType = response.msgType;
				var msg = response.msg;

				if (msgType == "success") {
					$("#subscribe_email").val('');
					$('.subscribe_msg').html('<p class="text-success">'+msg+'</p>');
				} else {
					$('.subscribe_msg').html('<p class="text-danger">'+msg+'</p>');
				}
				
				$('.sub_btn').html(sub_btn);
			}
		});
	});
	
}(jQuery));
