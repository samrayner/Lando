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

			var indent = new Array((4*nestLevel)+1);
			indent = indent.join("&nbsp;");

			$option.html(indent+page.title);

			if(page.current) {
				$option.attr("selected", 1);
			}

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

		$ul.replaceWith($select);
	}
};

$(function() {
	DropdownNav.init();
});