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

var Install = {
	done: function() {
		$("#install-button")
			.removeClass("active")
			.removeAttr("style")
			.addClass("done")
			.attr("data-icon", "2")
			.html("Installation complete");
	},

	updateProgress: function() {
		var $jqxhr = $.ajax({
			url: "install_log.txt",
			complete: function(data) {
				var response = data.responseText;
			
				if(!response)
					return window.setTimeout(Install.updateProgress, 500);
			
				var lines = response.split("\n");
				var total = lines[2].replace(/\D/g, '');
				
				lines.splice(0,4);
				var done = lines.length;
				
				if(done > total)
					return Install.done();
				
				if(done > 0) {
					$("#install-button").html(lines[done-1].replace(/^\t+/, ''));
					var width = $("#install-button").outerWidth();
					
					var percent = done/total*100;
					$("#install-button").css("background-position-x", Math.round(width*percent/100)+"px");
				}
				
				window.setTimeout(Install.updateProgress, 500);
			}
		});
	},
	
	run: function(type) {
		var $jqxhr = $.ajax({
			url: "content.php",
			data: {"host_root": $("host_root").val()},
			complete: Install.done
		});
		
		window.setTimeout(Install.updateProgress, 1000);
	},

	click: function(event) {
		event.preventDefault();
		$(this)
			.removeClass("done")
			.addClass("active")
			.attr("data-icon", "0")
			.html("Preparing files...");
		
		Install.run();
	},
	
	init: function() {
		$("#install-button").click(Install.click);
	}
};

$(function() {
	if($("#cleanup-button").length)
		CleanUp.init();
	
	if($("#install-button").length)
		Install.init();
});