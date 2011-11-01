<?php

class Controller {
	public $config;
	private $Model;
	public $theme_vars;
	
	public function __construct() {
		//make config available to methods and helper functions
		global $config;
		$this->config = $config;
		
		$this->Model = new Model();
		
		$this->define_theme_vars();
	}
	
	private function define_theme_vars() {
		$vars["site_title"] 			= $this->config["site_title"];
		$vars["site_description"] = $this->config["site_description"];
		$vars["theme_dir"] 				= "/themes/".$this->config["theme"];
		
		$vars["site_root"] = $this->config["site_root"];
		if(!$this->config["pretty_urls"])
			$vars["site_root"] .= "/index.php";
		
		if(!preg_match('~^(/file.php|/admin)~', $_SERVER["REQUEST_URI"]))
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
	
	private function loose_match($a, $b) {
		if(is_object($a))
			$a = get_object_vars($a);
	
		if(is_object($b))
			$b = get_object_vars($b);
	
		//if comparing value to array
		if(!is_array($a) && is_array($b)) {
			//if string, explode A as CSV and compare to B
			if(is_string($a)) {
				$arr = explode(",", $a);
				foreach($arr as $i => $str)
					$arr[$i] = trim($str);
				return $this->loose_match($arr, $b);
			}
			else //otherwise look for A in B
				return in_array($a, $b);
		}
		
		//if comparing array to value, fail
		elseif(is_array($a) && !is_array($b))
			return false;
		
		//if comparing arrays, B must contain A but any order
		elseif(is_array($a) && is_array($b))
			return (sizeof(array_diff($a, $b)) == 0);
		
		//otherwise convert to string and do case-insensitive comparison
		else
			return (strcasecmp($a, $b) == 0);
	}
	
	private function filter_by_props($content, $filters=array()) {
		if(is_array($filters) && !empty($filters)) {
			$filtered = array();
		
			foreach($content as $item) {
				$match = true;
			
				foreach($filters as $key => $val) {
					if(!isset($item->$key) || !$this->loose_match($val, $item->$key))
						$match = false;
						break;
				}
				
				if($match)
					$filtered[] = $item;
			}
			
			$content = $filtered;
		}
		
		return $content;
	}
	
	public function filter_content($content, $limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
		if(!is_array($content) || empty($content))
			return $content;
		
		$content = $this->filter_by_props($content, $filters);

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
		
		return array_offset_limit($content, $offset, $limit);
	}
	
	public function filter_collection($collection, $limit=0, $offset=0, $filters=array()) {
		if(empty($collection->files))
			return $collection;
		
		$collection->files = $this->filter_by_props($collection->files, $filters);
		$collection->files = array_offset_limit($collection->files, $offset, $limit);
		
		return $collection;
	}
	
	public function get_file($path, $thumb=false) {
		return $this->Model->get_file($path, $thumb);
	}
}