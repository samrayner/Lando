/*
Title:      	Admin Panel JavaScript
Author:     	Sam Rayner - http://samrayner.com
Created: 			2011-09-16
*/

PageNav = {
	sortableTree: function(parents) {
		var tree = {};
	
		for(var i = 0; i < parents.length; i++) {
			var parent = parents[i];
			tree[parent] = PageNav.sortableTree($("#"+parent+" > .sortable").sortable("toArray"));
			if(!$("#"+parent+" > * > input:checked").length)
				tree[parent]._hidden = true;
		}
		
		return tree;
	},
	
	updateOrder: function() {
		var topLevel = $("#page-list > ol").sortable("toArray");
		var tree =  PageNav.sortableTree(topLevel);
		$("#page_order").val(JSON.stringify(tree));
	},
	
	updateVisibility: function(event) {
		//get whether we're checking or unchecking
		var checking = event.target.checked;
	 
	 	//for all child checkboxes
		$(this).closest("li").find("* * input:checkbox").each(function() {
			//if we're unchecking, disable all children
			if(!checking)
				$(this).attr("disabled", true);
			
			//if checking	
			else {
				//get all grand-parent LIs
				$parentLis = $(this).closest("li").parentsUntil($("#page-list"),"li");
				
				var parentsUnchecked = $parentLis.children("div").children("input:not([checked])").length;
				
				//if we're checking, enable children who's parents are enabled
				if(!parentsUnchecked)
					$(this).removeAttr("disabled");
			}
		});
		
		PageNav.updateOrder();
	},
	
	init: function() {
		$("input:checkbox").change(PageNav.updateVisibility);
	
		//disable text selection so we can drag
		$("#page-list > ol").disableSelection();
		
		//make lists sortable
		$(".sortable").sortable({
				update: PageNav.updateOrder
		});
		
		//fire change on every checkbox
		$(".sortable input:checkbox").change();
	}
}

Tooltip = {
	toggle: function() {
		var $message = $("#htaccess");
		$message.toggleClass("collapsed");
	},

	init: function() {
		$("#pretty_urls").change(Tooltip.toggle);
	}
}

$(function() {
	Tooltip.init();
	PageNav.init();
});