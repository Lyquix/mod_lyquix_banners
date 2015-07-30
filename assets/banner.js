(function($){
	$.fn.Banner = function(options){
		return this.each(function() {
			// default settings
			this.settings = $.extend({
				// Default settings
				timer: null,		// Timer object for the banner
				play: true,		// Set to false to stop avoid automatic rotation
				delay: 5000, 		// Wait time in miliseconds before moving to next slide
				speed: 'slow',		// Speed of the transition effect
				current: 0, 		// Holds the number of the slide currently showing (starts with 0)
				banner: '.banner', 	// Class used to identify the DOM elements that contain banners
				tab: '.tab', 		// Class used to identify the DOM elements that contains the tabs
				prev: '.prev',		// Class used to identify the DOM element that contains the prev arrow
				next: '.next',		// Class used to identify the DOM element that contains the next arrow
				counter: '.counter', // Class used to identify the DOM element that contains the counter
			}, options );
			
			// banners, tabs and arrows objects
			var myBanner = this;
			var bannersWrap = $(this).children('div.banners');
			var banners = $(this).find(myBanner.settings.banner);
			var tabsWrap = $(this).children('div.tabs');
			var tabs = $(this).find(myBanner.settings.tab); 
			var prevarrow = $(this).find(myBanner.settings.prev); 
			var nextarrow = $(this).find(myBanner.settings.next);
			var counterWrap =  $(this).find(myBanner.settings.counter);
			
			// rotate to next and previous banner functions
			this.next = function(){
				if(myBanner.settings.current==banners.length-1) myBanner.rotate(0);
				else myBanner.rotate(myBanner.settings.current+1);
			}
			this.prev = function(){
				if(myBanner.settings.current==0) myBanner.rotate(banners.length-1);
				else myBanner.rotate(myBanner.settings.current-1);
			}
			// rotate banner function
			this.rotate = function(n) {
				// change class of tabs
				tabs.each(function(){
					if($(this).index()!=n) $(this).removeClass('on');
					else $(this).addClass('on');
				});
				
				// rotate banners
				banners.each(function(){
					if($(this).index()!==n) $(this).fadeOut(myBanner.settings.speed);
					else $(this).fadeIn(myBanner.settings.speed);
				});
				
				// update current banner
				myBanner.settings.current = n;
				
				// update data-num and counter div
				$(bannersWrap).attr('data-num',n+1);
				$(counterWrap).find('span.current').html(n+1);
				$(tabsWrap).attr('data-num',n+1);
				
				// continue playing
				if(myBanner.settings.play) myBanner.play();
			}
			// play function
			this.play = function(){
				clearTimeout(myBanner.settings.timer);
				myBanner.settings.timer = setTimeout((function(){myBanner.next();}),myBanner.settings.delay);
				myBanner.settings.play = true;
			}
			// pause function
			this.pause = function() {
				clearTimeout(myBanner.settings.timer);
				myBanner.settings.play = false;
			}
			// cycle through the banners
			banners.each(function(){
				// hide all banners except for the first one
				if($(this).index()>0) $(this).css({'display':'none'});
			});
			// cycle through the tabs
			tabs.each(function(){
				// set the initial on class
				if($(this).index()==0) $(this).addClass('on');
				
				// add listener for click event
				$(this).click(function(){myBanner.rotate($(this).index());});
				
			});
			// start automatic rotation
			if(myBanner.settings.play) myBanner.play();
			// setup listeners for arrows
			$(prevarrow).click(function(){myBanner.prev();});
			$(nextarrow).click(function(){myBanner.next();});			
			// return object
			return this;
		});
	};
}(jQuery));