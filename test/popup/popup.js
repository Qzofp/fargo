jQuery(function($) {

	$("td").click(function() {
			//loading(); // loading
			
			var currentCellText = $(this).children(".hidden").text();
			//setTimeout(function(){ // then show popup, delay in .5 second
				loadPopup(currentCellText); // function show popup
			//}, 100); // .5 second
	return false;
	});

	/* event for close the popup */
	$("div.close").hover(
					function() {
						$('span.ecs_tooltip').show();
					},
					function () {
    					$('span.ecs_tooltip').hide();
  					}
				);

	$("div.close").click(function() {
		disablePopup();  // function close pop up
	});

	$(this).keyup(function(event) {
		if (event.which == 27) { // 27 is 'Ecs' in the keyboard
			disablePopup();  // function close pop up
		}
	});

	$('a.livebox').click(function() {
		alert('Hello World!');
	return false;
	});

	 /************** start: functions. **************/
	function loading() {
		$("div.loader").show();
	}
	function closeloading() {
		$("div.loader").fadeOut('normal');
	}

	var popupStatus = 0; // set value
	
	function loadPopup(cell) {
		if(popupStatus == 0) { // if value is 0, show popup
			closeloading(); // fadeout loading

			$("#background_popup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
			$("#background_popup").fadeIn(700);
			
			$("#popup").fadeIn(500); // fadein popup div
			
			$("#cell").text('').append("<b>Current Cell Text: </b>" + cell + "<br/>");
			
			popupStatus = 1; // and set value to 1
		}
	}

	function disablePopup() {
		if(popupStatus == 1) { // if value is 1, close popup
			$("#popup").fadeOut("normal");
			$("#background_popup").fadeOut("normal");
			popupStatus = 0;  // and set value to 0
		}
	}
	/************** end: functions. **************/
}); // jQuery End
