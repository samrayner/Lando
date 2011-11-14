/*
Title:				Admin Panel JavaScript
Author:				Sam Rayner - http://samrayner.com
Created:			2011-09-16
*/

var PageNav = {
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
				var $parentLis = $(this).closest("li").parentsUntil($("#page-list"),"li");
				
				var parentsUnchecked = $parentLis.children("div").children("input:not([checked])").length;
				
				//if we're checking, enable children who's parents are enabled
				if(!parentsUnchecked)
					$(this).removeAttr("disabled");
			}
		});
		
		PageNav.updateOrder();
	},
	
	labelTap: function() {
		var $checkbox = $("#".$(this).attr("for"));
		var checked = $checkbox.checked;
		
		if(checked)
			$checkbox.removeAttr("checked");
		else
			$checkbox.attr("checked", 1);
	},
	
	init: function() {
		$("#page-list input:checkbox").change(PageNav.updateVisibility);
		$("#page-list label").click(PageNav.labelTap);
	
		//disable text selection so we can drag
		$("#page-list > ol").disableSelection();
		
		//make lists sortable
		$(".sortable").sortable({
				update: PageNav.updateOrder
		});
		
		//fire change on every checkbox
		$(".sortable input:checkbox").change();
	}
};

var Tooltip = {
	toggle: function() {
		var $message = $("#htaccess");
		$message.toggleClass("collapsed");
	},

	init: function() {
		$("#pretty_urls").change(Tooltip.toggle);
	}
};

var Recache = {
	types: ["pages", "posts", "drafts", "collections", "snippets"],

	done: function() {
		$("#recache-button").removeClass("active").html("Cache refresh complete");
	},

	updateProgress: function(current) {
		$("#recache-button").html("Caching "+current+"&hellip;");
	},
	
	create: function(type) {
		Recache.updateProgress(type);
	
		var $jqxhr = $.ajax({
			url: "recache/create_cache.php",
			data: {"type": type},
			complete: function() { 
				var pos = Recache.types.indexOf(type);
					if(pos+1 == Recache.types.length)
						Recache.done();
				else
					Recache.create(Recache.types[pos+1]);
			}
		});
	},

	click: function(event) {
		event.preventDefault();
		$(this).addClass("active");
		var $jqxhr = $.ajax({
			url: "recache/clear_caches.php",
			always: function(){ 
				Recache.create(Recache.types[0]); 
			}
		});
	},
	
	init: function() {
		$("#recache-button").click(Recache.click);
	}
};

var PassChange = {
	validate: function(event) {
		if($("#admin_password").val() != $("#confirm_pass").val()) {
			window.alert("The passwords you entered don't match, please type them again.");
			$("#admin_password").val("");
			$("#confirm_pass").val("");
			$("#admin_password").focus();
			return false;
		}
	},

	init: function() {
		$("#admin-form").submit(PassChange.validate);
	}
};

$(function() {
	Tooltip.init();
	Recache.init();
	PageNav.init();
	PassChange.init();
});