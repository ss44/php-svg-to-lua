SVG to LUA
==================

A basic script that takes an svg document and converts it into a lua table where, each group (or layer depending on your program) is divided up into a separate index based on the group/layer name.

In addition to all the fields that SVG stores the following additional _convenience_ fields are also calculated:

 - xCenter / yCenter : The center coordinates of a shape.
 - rotate : The rotation angle in degrees.

 
Usage
------------------
	php svgToLua.php <filename.svg> 

Why?
-----------------
To make it possible to create level maps using any SVG editor, and have that information quickly translated so it could be used by frameworks such as Ansca Mobile's Corona.

Todo
-----------------
A lot. This is just a starting point for now, and while it presents all the information given by each svg node, it's only been tested with rectangles so far. As I use it to build more complex levels, I'll continue to add to it, but feel free to contribute if you find it useful. 

Extending
-----------------
Just add a method:

	_svgWhatYourFunctionDoes( SimpleXMLElement ) 
That prints out:

	", newParams = 'values'"

be sure to check that the element passes the attributes you need.