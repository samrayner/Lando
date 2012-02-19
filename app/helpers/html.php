<?php

function page_nav($blog_text="Blog", $pages=null, $path=array()) {
	global $Lando;
	$page_order = $Lando->config["page_order"];

	if(!$pages) { //first run-through
		$html = '<nav class="page-nav">'."\n";

		$pages = pages();

		$pages[] = new Page(array(
			"slug" => "posts",
			"title" => $blog_text,
			"permalink" => "/posts/"
		));

		$html .= page_nav($blog_text, $pages);
		$html .= '</nav>';
		return $html;
	}
	
	$active_class = "current";
	$parent_class = "parent";
	
	$url = current_path();
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = "$tabs<ul>\n";

	foreach($pages as $Page) {
		$path[] = $Page->slug();
		
		$active = $page_order;
		foreach($path as $next_key) {
			if(isset($active[$next_key]))
				$active = $active[$next_key];
		}
		
		if(!isset($active["_hidden"]) || $active["_hidden"] == false) {
			$path_str = "/".implode("/", $path)."/";
			
			if($url == "/")
				$url = "/home/";
			
			$active = (strpos($url, rtrim($path_str, "/")) === 0);
			$subpages = $Page->subpages();
	
			$html .= "$tabs\t<li";
			
			$classes = array();
			
			if($active)
				$classes[] = $active_class;
				
			if(!empty($subpages))
				$classes[] = $parent_class;
			
			if(!empty($classes)) 
				$html .= ' class="'.implode(" ", $classes).'"';
			
			$html .= ">\n$tabs\t\t".'<a href="'.$Page->permalink().'">'.$Page->title()."</a>\n";
	
			if(!empty($subpages))
				$html .= page_nav($blog_text, $subpages, $path);
	
			$html .= "$tabs\t</li>\n";
		}
		
		array_pop($path);
	}

	$html .= "$tabs</ul>\n";
	
	return $html;
}

function page_breadcrumbs($path=null) {
	if(!$path)
		$path = current_path();

	$Page = page($path);

	if(!$Page)
		return false;

	$html = '<nav class="page-breadcrumbs">'."\n\t<ul>";

	$parents = $Page->parents();

	foreach($parents as $Parent) {
		$html .= "\n\t\t<li>";
		$html .= '<a href="'.$Parent->permalink().'">'.$Parent->title().'</a>';
		$html .= "</li>";
	}

	$html .= "\n\t\t<li>".$Page->title()."</li>";

	$html .= "\n\t</ul>\n</nav>";

	return $html;
}

//http://www.roscripts.com/snippets/show/32
function ascii_to_entities($str) {
	$count	= 1;
	$out	= '';
	$temp	= array();

	for ($i = 0, $s = strlen($str); $i < $s; $i++) {
		$ordinal = ord($str[$i]);

		if ($ordinal < 128)
			$out .= $str[$i];
		else {
			if (count($temp) == 0)
				$count = ($ordinal < 224) ? 2 : 3;

			$temp[] = $ordinal;

			if (count($temp) == $count) {
				$number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);

				$out .= '&#'.$number.';';
				$count = 1;
				$temp = array();
			}
		}
	}
	return $out;
}

function dropdown($options, $selected=null, $attr=array()) {
	if(!is_array($attr))
		return false;

	$html = '<select';
	
	foreach($attr as $key => $val)
		$html .= ' '.$key.'="'.$val.'"';
		
	$html .= '>';
	
	foreach($options as $option) {
		$html .= '<option';
		
		if(strnatcasecmp($option, $selected) == 0)
			$html .= " selected";
			
		$html .= '>'.$option.'</option>';
	}
	
	$html .= "</select>";
	
	return $html;
}

//from Cake.php framework
function truncate_html($text, $length = 100, $ending = '&hellip;', $exact = false, $considerHtml = true) {
	if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}

// HTML Compressor 1.0.0
// This code is licensed under the MIT Open Source License.
// Copyright (c) 2010 tylerhall@gmail.com
// Latest Source and Bug Tracker: http://github.com/tylerhall/html-compressor
function compress_html($data)
{
	$data 			= $data."\n";
	$out        = '';
	$inside_pre = false;
	$bytecount  = 0;

	while($line = get_line($data))
	{
		$bytecount += strlen($line);

		if(!$inside_pre)
		{
			if(strpos($line, '<pre') === false)
			{
				// Since we're not inside a <pre> block, we can trim both ends of the line
				$line = trim($line);
				
				// And condense multiple spaces down to one
				$line = preg_replace('/\s\s+/', ' ', $line);
			}
			else
			{
				// Only trim the beginning since we just entered a <pre> block...
				$line = ltrim($line);
				$inside_pre = true;

				// If the <pre> ends on the same line, don't turn on $inside_pre...
				if((strpos($line, '</pre') !== false) && (strripos($line, '</pre') >= strripos($line, '<pre')))
				{
					$line = rtrim($line);
					$inside_pre = false;
				}
			}
		}
		else
		{
			if((strpos($line, '</pre') !== false) && (strripos($line, '</pre') >= strripos($line, '<pre')))
			{
				// Trim the end of the line now that we found the end of the <pre> block...
				$line = rtrim($line);
				$inside_pre = false;
			}
		}

		// Filter out any blank lines that aren't inside a <pre> block...
		if($inside_pre || $line != '')
			$out .= $line . "\n";
	}

	// Remove the trailing \n
	return trim($out);
}

// Returns the next line from a string
function get_line(&$data)
{
	if(strlen($data) > 0)
	{
		$pos = strpos($data, "\n");
		$return = substr($data, 0, $pos) . "\n";
		$data = substr($data, $pos + 1);
		return $return;
	}
	else
		return false;
}