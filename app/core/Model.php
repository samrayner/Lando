<?php 

class Model {
	private $Host;
	private $Cache;

	public function __construct() {
		global $config;
		
		$host_class = str_replace(" ", "_", ucwords($config["host"]));		
		$this->Host = new $host_class();
		$this->Cache = new Cache();
	}
	
	public function get_host_account_info() {
		return $this->Host->account;
	}
	
	public function get_posts() {
		$slugs = $this->Host->get_subdirs($this->Host->content_root."/posts");
		
		$posts = array();
		
		foreach($slugs as $slug)
			$posts[] = $this->get_post($slug);
			
		$this->Cache->update("posts", $posts);
		
		return $posts;
	}
	
	public function get_post($slug) {
		//search cache
		$cache_route = array_search_recursive($slug, $this->Cache->posts, "slug", false);
		
		if(!$cache_route) {
			$post = $this->Host->get_post($slug);
			$this->Cache->add("posts", $post);
			return $post;
		}
		
		array_pop($cache_route);
		
		//construct array call from searched nodes
		eval('$post = $this->Cache->posts["'.implode('"]["', $nodes).'"];');
		
		return $post;
	}
}