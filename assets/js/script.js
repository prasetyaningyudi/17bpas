function toggleFullScreen() {
    var a = $(window).height() - 10;
    if (!document.fullscreenElement && // alternative standard method
        !document.mozFullScreenElement && !document.webkitFullscreenElement) { // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
    $('.full-screen').toggleClass('icon-maximize');
    $('.full-screen').toggleClass('icon-minimize');
}


$(document).ready(function() {
	var $BODY = $('body');
	$('.button-filter, .button-filter-close').on('click', function() {
		
		if ($BODY.hasClass('full-width')) {
			$SIDEBAR_MENU.find('li.active ul').hide();
			$SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
			$BODY.removeClass('nav-md');
			$BODY.addClass('nav-sm');
		} else {
			if(localStorage.getItem("sidebar_state") !== "full-width"){
				$SIDEBAR_MENU.find('li.active-sm ul').show();
				$SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
				$BODY.removeClass('nav-sm');
				$BODY.addClass('nav-md');
			}
		}

		$('.dataTable').each ( function () { $(this).dataTable().fnDraw(); });
	
		console.log('clicked - filter button');
		$BODY.toggleClass('full-width semi-width');
	});
	
	
	if (typeof(Storage) === "undefined") {
		
	}else{
		if(localStorage.getItem("sidebar_state") == "full-width"){
			$SIDEBAR_MENU.find('li.active ul').hide();
			$SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
			$BODY.removeClass('nav-md');
			$BODY.addClass('nav-sm');
		}else if(localStorage.getItem("sidebar_state") == "semi-width"){
			$SIDEBAR_MENU.find('li.active-sm ul').show();
			$SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
			$BODY.removeClass('nav-sm');
			$BODY.addClass('nav-md');			
		}	
	}	
});