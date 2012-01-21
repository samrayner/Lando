<?php

//Based on: http://www.php.net/manual/en/function.array-search.php#68424
function array_search_recursive($needle_val, $haystack, $needle_key=null, $regex=false, $path=array()) {
  if(!is_array($haystack))
		return false;
	
  foreach($haystack as $key => $val) {
  	if(is_object($val) && method_exists($val, "export"))
  		$val = $val->export();
 
  	if($regex)
			$val_match = preg_match((string)$needle_val, (string)$val);
		else
			$val_match = strtolower((string)$val) === strtolower((string)$needle_val);
			
		$key_match = $needle_key ? strtolower((string)$key) === strtolower((string)$needle_key) : true;

  	//if value is an array, drill down
		if(is_array($val) and $sub_path = array_search_recursive($needle_val, $val, $needle_key, $regex, $path)) {
			$path = array_merge($path, array($key), $sub_path);
			return $path;
		}
		//if value is a terminal node
		elseif($val_match && $key_match) {
			$path[] = $val;
			return $path;
		}
  }
  return false;
}

function array_flatten($array, $return=array()) {
	foreach ($array as $value) {
		if(is_array($value))
			$return = array_flatten($value, $return);
		else {
			if($value)
				$return[] = $value;
		}
	}
	return $return;
}

function parent_key($array, $value) {
	$route = array_search_recursive($value, $array);
	
	if(isset($route[sizeof($route)-2]))
		return $route[sizeof($route)-2];
	
	return null;
}

function array_offset_limit($array, $offset, $limit) {
	if(!is_array($array) || empty($array))
		return $array;

	//correct offset to array bounds
	if($offset < 0)
		$offset = 0;
	elseif($offset > sizeof($array))
		$offset = sizeof($array);
	
	//chop off everything before offset
	$array = array_slice($array, $offset);
	
	//correct limit to array bounds
	if($limit < 1 || $limit > sizeof($array))
		$limit = sizeof($array);
	
	//chop off everything after limit
	array_splice($array, $limit);
	
	return $array;
}

function array_insert($array, $new_element, $index=0) {
	$result = array_splice($array, 0, $index);
	$result[] = $new_element;
	return array_merge($result, $array);
}