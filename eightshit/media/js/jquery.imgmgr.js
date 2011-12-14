(function($){  

$.fn.imgmgr = function(options) {  
	
	// SET THE DEFAULTS
	var defaults = {
		fadeLength: 5000,
		fadeStart: 2000,
		loaderPath: "i/loader.gif"
	};

	// ALLOWS USER TO INPUT OPTIONS TO OVERRIDE DEFAULTS
	var options = $.extend(defaults,options);

	// DON'T REALLY KNOW WHY WE ARE RETURNING HERE
	return this.each(function() {
	
		// SETTING 'THIS' TO A VARIABLE
		obj = $(this);
		var thisImage = $(this);
		
		// GET THE WIDTH ATTRIBUTE OF THE IMAGE
		var thisWidth = obj.attr("width");
		
		// GET THE HEIGHT ATTRIBUTE OF THE IMAGE
		var thisHeight = obj.attr("height");
		
		// HIDE ALL THE IMAGES
		$(this).hide();

		// WRAP THE IMAGE IN A DIV WITH THE SAME HEIGHT AND WIDTH DIMENSIONS THAT THE IMAGE HAD
		obj.wrap("<div class='img_wrap' style='width:"+thisWidth+"px; height:"+thisHeight+"px;'></div>");
		
		obj.parent().css({
			background:"url("+options.loaderPath+") center no-repeat"
		});
		
		// WHEN THE IMAGE LOADS
		obj.load(function(){
			
			// WAIT A BIT
			setTimeout(function(){
				
				// THEN FADE IN THE IMAGE
				// THIS IS WHERE WE SEEM TO BE HAVING THE PROBLEM
				thisImage.fadeIn(options.fadeLength,function(){
				
				});
			// THIS IS HOW LONG WE ARE WAITING (THE SET-TIMEOUT
			},options.fadeStart);
		});

	});  

};

$.fn.imgmgrbg = function(options) {  
	var defaults = {
		switchLength: 5000,
		tempBg: "#777",
		opacity: "1"
	};
	
	var imageQue = new Array();
	var bgImages = new Array();
	
	var count = 0;
	
	// ALLOWS USER TO INPUT OPTIONS TO OVERRIDE DEFAULTS
	var options = $.extend(defaults,options);
	var elements = this;
	// DON'T REALLY KNOW WHY WE ARE RETURNING HERE
	return this.each(function(i) {
		
		// SETTING 'THIS' TO A VARIABLE
		obj = $(this);
		
		// GET THE PATH FOR THE BACKGROUND IMAGE FROM THE CSS PROPERTY
		var grabBgImg = obj.css("background-image");
		bgImages[i] = grabBgImg;
		
		// SET THE CSS BACKGROUND IMAGE TO NONE AND SET THE BACKGROUND COLOR TO WHATEVER IS DEFINED BY THE PLUGIN
		obj.css({
			backgroundImage:"none",
			backgroundColor: options.tempBg,
			opacity: options.opacity
		});
		
		// CREATE A NEW IMAGE
		var loadImage = new Image();
		
		// SET THE SOURCE OF THE IMAGE TO BE THE BACKGROUND CSS IMAGE
		loadImage.src = grabBgImg;
		
		imageQue[i] = loadImage;
		
		
		loadImage.onload = function(){
			
			for(var bgImage in imageQue){
				if(!imageQue[bgImage].complete){
					return false;
				}
			}
			
			setTimeout(function(){
				for(j=0;j<imageQue.length;j++){			
					jQuery(elements.get(j)).css({
						backgroundImage: bgImages[j]
					});
					jQuery(elements.get(j)).animate({
						opacity:"1"
					},10000)
				}
			},5000);
		};
		
	});
};

})(jQuery);