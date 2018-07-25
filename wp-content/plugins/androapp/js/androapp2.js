function switchMode(mode){
	if(mode == 'compact'){
		switchToCompactMode();
	}else{
		switchToCardViewMode();
	}
}

function changeHomePageIdTextboxText(val){
	jQuery("#homepage_id_textbox").val("");
	var placeholder = "enter "+val;
	if(val == "author" || val =="tag" || val == "product-cat" || val == "product-tag"){
		placeholder += " slug";
	}else{
		placeholder += " id";
	}
	jQuery("#homepage_id_textbox").attr("placeholder", placeholder);
}

function switchFont(value){
	if(value == ""){
		jQuery("#statusBar").css('font-family', "");
		jQuery("#actionBarTitle").css('font-family', "");
		jQuery("#androScreen").css('font-family', "");
	}
	else{
		//value = "Arvo-Italic";
		//alert('switcin'+ jQuery("#"+value).attr('fontname'));	
		var fontname = jQuery("#"+value).attr('fontname');
		var fontFamily = jQuery("#"+value).attr('ffamily');

		WebFontConfig = {
			google: { families: [ fontname+':latin' ] }
		  };
	  (function() {
		var wf = document.createElement('script');
		wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
		
		jQuery("#statusBar").css('font-family', fontFamily);
		jQuery("#actionBarTitle").css('font-family', fontFamily);
		jQuery("#androScreen").css('font-family', fontFamily);
	  })();
	}
}
function switchToCardViewMode(){
	var str = "div[name='androPostImage']";
	jQuery( str ).removeClass("androPostImageCompact");
	jQuery( str ).addClass("androPostImageCardview");

	var imgSrcStr = "img[name='androPostImageSrc']";
	jQuery( imgSrcStr ).removeClass("androPostImageSrcCompact");
	jQuery( imgSrcStr ).addClass("androPostImageSrcCardview");
	
	var noImgStr = "img[name='androPostNoImage']";
	jQuery(noImgStr).css("display", "none");
	
	var titlestr = "div[name='androPostTitle']";
	jQuery( titlestr ).removeClass("androPostTitleCompact");
	jQuery( titlestr ).addClass("androPostTitleCardview");
	
	var contentStr = "div[name='androPostContent']";
	jQuery( contentStr ).removeClass("giveMeEllipsis");
	jQuery( contentStr ).removeClass("androPostContentCompact");
	jQuery( contentStr ).addClass("androPostContentCardview");
	
	var tagStr = "div[name='tagTitle']";
	jQuery( tagStr ).css("display", "block");
	
	
	var authorStr = "div[name='author']";
	jQuery( authorStr ).css("display", "none");
	
	var timeStr = "div[name='timeAgo']";
	jQuery(timeStr).css("display", "none");
	
	
}

function switchToCompactMode(){
	var str = "div[name='androPostImage']";
	jQuery( str ).removeClass("androPostImageCardview");
	jQuery( str ).addClass("androPostImageCompact");

	var imgSrcStr = "img[name='androPostImageSrc']";
	jQuery( imgSrcStr ).removeClass("androPostImageSrcCardview");
	jQuery( imgSrcStr ).addClass("androPostImageSrcCompact");
	
	var noImgStr = "img[name='androPostNoImage']";
	jQuery(noImgStr).css("display", "block");
	
	var titlestr = "div[name='androPostTitle']";
	jQuery( titlestr ).removeClass("androPostTitleCardview");
	jQuery( titlestr ).addClass("androPostTitleCompact");
	
	var contentStr = "div[name='androPostContent']";
	jQuery( contentStr ).addClass("giveMeEllipsis");
	jQuery( contentStr ).removeClass("androPostContentCardview");
	jQuery( contentStr ).addClass("androPostContentCompact");
	
	
	var tagStr = "div[name='tagTitle']";
	jQuery( tagStr ).css("display", "none");
	
	var authorStr = "div[name='author']";
	jQuery( authorStr ).css("display", "block");
	
	var timeStr = "div[name='timeAgo']";
	jQuery(timeStr).css("display", "block");
	
}

function changeBgColor(divName, color){
	var str = "div[name='" + divName+ "']";
	jQuery( str ).css("background", color);
}

function changeTextColor(divName, color){
	var str = "div[name='" + divName+ "']";
	jQuery( str ).css("color", color);
}