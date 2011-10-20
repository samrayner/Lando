<?php

class CSV_Parser {
	public function parse($str) {
		$html = "<table>\n\t<thead>\n";
		
		foreach(explode("\n", $str) as $i => $row) {		
			$html .= "\t\t<tr>";
			
			$cell_tag = ($i == 0) ? "th" : "td";
			
			foreach(explode(",", $row) as $cell)
				$html .= "\n\t\t\t<$cell_tag>".htmlspecialchars($cell)."</$cell_tag>";

			$html .= "\n\t\t</tr>\n";
			
			if($i == 0)
				$html .= "\t</thead>\n\t<tbody>\n";
		}
		
		$html .= "\t</tbody>\n</table>";
		
		return $html;
	}
}