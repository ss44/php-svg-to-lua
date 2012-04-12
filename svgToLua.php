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



/**
 * core function to parse the svg file.
 */
function svgToLua( $filename ){

	// Get the contents of the svg file.
	$content = file_get_contents( $filename );
	$userMethods = userParseMethods();

	$svg = new SimpleXMLElement( $content );
	$svg->svg;
	$groups = array();
	echo "module(...)\n";
	echo "svg = {\n";

	$notLast = array(false, false, false);
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

			$notLast[2] = false;

			// Save all the standard attributes
			foreach ( $childNode->attributes() as $key => $value ){
				if ( $notLast[2] ){
					echo ', ';
				}

				$key = str_replace('-', '_', $key);
				echo "$key = '$value' "; 

				$notLast[2] = true;
			}

			// Run some of our custom modules against this element and add these to the object also.
			foreach( $userMethods as $method ){
				call_user_func( $method, $childNode );
			}

			echo "}";
			$notLast[1] = true;
		}	

		echo "\n\t".'}';
		$notLast[0] = true;
	}
	
	

	echo "\n}\n";
}

function _svgCalculateRotation( $node ){

	if ( isset($node['transform'] ) ){
		$transformation = $node['transform'];

		// Deal with the matrix transforatmion
		if ( !preg_match( '/matrix\((.*)\)/', $transformation, $tmp ) ){
			return;
		}

		// Explode the coords
		$coords = explode(' ', $tmp[1]);
		$angle = atan2($coords[2], $coords[0]);
		
		// Convert from radians to degrees
		$angle = round($angle * 180 / pi(), 2);

		echo ", rotate = '$angle'";	
	}	
}

// Calculate the center x, y coords
function _svgCalculateCenters( $node ){

	if (isset($node['x'], $node['y'], $node['height'], $node['width'] )){
		$centerX = round( $node['width'] / 2 + $node['x'], 2);
		$centerY = round( $node['height'] / 2 + $node['y'], 2);

		echo ", xCenter = '$centerX', yCenter = '$centerY' ";
	}
}

function userParseMethods(){
	$methods = get_defined_functions();
	$definedMethods = array();
	foreach ( $methods['user'] as $method ){
		if (strpos($method, '_svg') !== false){
			$definedMethods[] = $method;
		}
	}
	return $definedMethods;
}

function showUsage(){

	echo "\nsvgToLua.php file.svg\n";
	exit();
}