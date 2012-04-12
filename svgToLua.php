<?php
 /**
  * Converts an SVG document to lua tables. Each group is treated as its own table.
  * 
  * Usage:
  *		svgToLua <file.svg>
  *
  * @author Shajinder Singh <ss@ss44.ca>
  * @created 11-April-2012
  */

if (!array_key_exists(1, $argv)){
	showUsage();
}

$svgFileName = $argv[1];

svgToLua( $svgFileName );

function svgToLua( $filename ){

	// Get the contents of the svg file.
	$content = file_get_contents( $filename );

	$svg = new SimpleXMLElement( $content );
	$svg->svg;
	$groups = array();

	echo "svg = {\n";

	$notLast = array(false, false);
	foreach ( $svg->g as $group ){

		if ( $notLast[0] ){
			echo ",\n";
		}

		echo "\t$group[id] = {\n";

		$notLast[1] = false;
		foreach( $group->children() as $childNode ){
			if ( $notLast[1] ){
				echo ",\n";
			}

			echo "\t\t{";

			$type = $childNode->getName();
			echo "shape = '$type', ";

			foreach ( $childNode->attributes() as $key => $value ){
				echo "$key = '$value' "; 
			}

			echo "}";
			$notLast[1] = true;
		}	

		echo "\n\t".'}';
		$notLast[0] = true;
	}
	
	

	echo "\n}\n";

}

function showUsage(){

	echo "\nsvgToLua.php file.svg\n";
	exit
}