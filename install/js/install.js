/*
Title:			Install Wizard Javascript
Author:			Sam Rayner - http://samrayner.com
Created:		2011-12-28
*/

var CleanUp = {
	done: function() {
		$("#cleanup-button")
			.removeClass("active")
			.addClass("done")
			.attr("data-icon", "2")
			.html("Clean-up complete");
	},

	click: function(event) {
		event.preventDefault();
		
		$(this)
			.removeClass("done")
			.addClass("active")
			.attr("data-icon", "0")
			.html("Deleting install files");
			
		var $jqxhr = $.get("cleanup.php", CleanUp.done);
	},
	
	init: function() {
		$("#cleanup-button").click(CleanUp.click);
	}
};

$(function() {
	if($("#cleanup-button").length)
		CleanUp.init();
});