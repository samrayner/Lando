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
		var topLevel = $("#page-list").sortable("toArray");
		var tree =  PageNav.sortableTree(topLevel);
		$("#page_order").val(JSON.stringify(tree));
	},
	
	updateVisibility: function(event) {
		//get whether we're checking or unchecking
		var checking = event.target.checked;
	 
	 	//for all child checkboxes
		$(this).closest("li").find("* * input:checkbox").each(function() {
			//find whether the parent is checked
			$parentLi = $(this).closest("li").parent().parent();
			var parentChecked = $parentLi.children("div").children("input:checked").length;
			
			//if we're unchecking, disable all children
			if(!checking)
				$(this).attr("disabled", true);
			
			//if we're checking, enable children who's parents are enabled
			if(checking && parentChecked)
				$(this).removeAttr("disabled");
		});
		
		 PageNav.updateOrder();
	},
	
	init: function() {
		$("input:checkbox").change(PageNav.updateVisibility);
	
		//disable text selection so we can drag
		$("#page-list").disableSelection();
		
		//make lists sortable
		$(".sortable").sortable({
				update: PageNav.updateOrder
		});
		
		//fire change on every checkbox
		$(".sortable input:checkbox").change();
	}
}

$(function() {
	PageNav.init();
});