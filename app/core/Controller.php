<?php

class Controller {
	public $config;
	private $Model;
	public $theme_vars;
	
	public static $instance;
	public static function get_instance() {
		if(!self::$instance)
			self::$instance = new Controller();
		
		return self::$instance;
	}
	
	public function __construct() {
		//make config available to methods and helper functions
		global $config;
		$this->config = $config;
		
		$this->Model = new Model();
		$this->content_root = $config["host_root"]."/".$config["site_title"];
		
		$this->define_theme_vars();
	}
	
	private function define_theme_vars() {
		$vars["site_title"] 			= $this->config["site_title"];
		$vars["site_description"] = $this->config["site_description"];
		$vars["theme_dir"] 				= "/themes/".$this->config["theme"];
		
		$vars["site_root"] = $this->config["site_root"];
		if(!$this->config["pretty_urls"])
			$vars["site_root"] .= "/?";
			
		$vars["current"] = $this->get_content();
		
		$this->theme_vars = $vars;
	}
	
	private function get_content_path($url=null) {
		if(!$url)
			$url = current_url();
		
		if($url == "/")
			$url .= "home";

		$url = explode("/", trim_slashes($url));

		if(isset($url[0]) && in_array($url[0], array("posts", "drafts")))
			$root = array_shift($url);
		else
			$root = "pages";

		array_unshift($url, $root);

		return implode("/", $url);;
	}
	
	public function get_content($type=null, $names=null) {
		if(!$type && !$names) //get content for current page
			return $this->Model->get_single($this->get_content_path());
		
		if(!$type) //must provide a content type
			return false;
	
		if(!$names) //get unfiltered
			return $this->Model->get_all($type);
		
		if(is_string($names)) //filter to individual content
			return $this->Model->get_single("$type/$names");
			
		if(is_array($names)) { //filter to multiple content
			$items = array();
		
			foreach($names as $name)
				$items[] = $this->Model->get_single("$type/$name");
			
			return $items;
		}
		
		return false;
	}
	
	public function filter_content($content, $limit, $offset, $year, $month, $day) {
		if(!is_array($content) || empty($content))
			return $content;

		$year 	= (int)$year;
		$month 	= (int)$month;
		$day 		= (int)$day;

		if($year) {
			//sort into date array
			$by_date = array(array(array(array())));
			$date_types = array("published", "created");
			
			foreach($content as $item) {
				foreach($date_types as $type) {
					if(isset($item->$type))
						$date = $item->$type;
				}
				
				if(isset($date))
					$by_date[date('Y', $date)][date('n', $date)][date('d', $date)][] = $item;
			}
			
			//flatten down array to filter level
			if(!isset($by_date[$year]))
				$by_date = array();
			elseif($month) {
				if(!isset($by_date[$year][$month]))
					$by_date = array();
				elseif($day) {
					if(!isset($by_date[$year][$month][$day]))
						$by_date = array();
					else
						$by_date = array_flatten($by_date[$year][$month][$day]);
				}
				else
					$by_date = array_flatten($by_date[$year][$month]);
			}
			else
				$by_date = array_flatten($by_date[$year]);
			
			$content = $by_date;
		}
		
		//correct offset to array bounds
		if($offset < 0)
			$offset = 0;
		elseif($offset > sizeof($content))
			$offset = sizeof($content);
		
		//chop off everything before offset
		$content = array_slice($content, $offset);
		
		//correct limit to array bounds
		if($limit < 1 or $limit > sizeof($content))
			$limit = sizeof($content);
		
		//chop off everything after limit
		array_splice($content, $limit);
		
		return $content;
	}
	
	public function get_file($path, $thumb=false) {
		return $this->Model->get_file($path, $thumb);
	}
}