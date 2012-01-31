/*
Title:			Global Theme jQuery
Author:			Sam Rayner - http://samrayner.com
Created:		2012-01-31
*/

var DropdownNav = {
	listToArray: function(list) {
		var nestLevel = [];

		$.each($(list).children("li"), function(i, val) {
			var $link = $(this).children("a:first");

			nestLevel[i] = {title: $link.text()};
			nestLevel[i].href = $link.attr("href");
			nestLevel[i].current = $(this).hasClass("current");

			var $subpages = $(this).children("ul:first");

			if($subpages.length) {
				nestLevel[i].subpages = DropdownNav.listToArray($subpages.get());
      }
		});

		return nestLevel;
	},

	addOptions: function($select, pages, nestLevel) {
		$.each($(pages), function(i, page) {
			var $option = $('<option />');
			$option.attr("value", page.href);
			$option.attr("selected", page.current);

			var indent = new Array(nestLevel+1);
			indent = indent.join("&rarr; ");

			$option.html(indent+page.title);

			$select.append($option);

			if(page.subpages) {
				DropdownNav.addOptions($select, page.subpages, nestLevel+1);
			}
		});
	},

	init: function() {
		var $ul = $("nav.page-nav ul:first");
		var list = DropdownNav.listToArray($ul.get());

		var $select = $('<select />');
		
		DropdownNav.addOptions($select, list, 0);

		$select.change(function(){
			window.location.href = $select.children("option:selected").val();
		});

		$ul.replaceWith($select);
	}
};

$(function() {
	if(window.matchMedia('screen and (max-width: 700px)').matches) {
		DropdownNav.init();
	}
});