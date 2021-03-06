<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schie�l
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * get the cylce value for pnRender template engine
 * to specify how many pictures should be shown in one
 * row
 *
 * @param	int
 * @return	string
 */
function up_getCycle($cols)
{
  	for ($i=1;$i<$cols;$i++) {
	    $c.=",";
	}
	$c.="<br class=\"userpictures_clearboth\" />";
	return $c;
}

/**
 * prepare string to be display in title-tag
 * @param	string
 * @return 	string
 */
function up_prepDisplay($s)
{
  	return htmlentities(str_replace("'","\'",$s));
}
/**
 * build where string for sql statement
 *
 * @param	array
 * @result	string
 */
function up_constructWhere($w)
{
  	// construct where statement for sql	
  	$first = true;
  	unset($where);
	foreach ($w as $a)	{
	  	if (!($first)) $where.=" AND ";
	  	$where.= $a;
	  	$first = false;		  
	}
	return $where;
}

/**
 * get height value for thumbnail's size in view function
 *
 * @result	int
 */
function up_getThumbnailHeight()
{
  	$size = pnModGetVar('UserPictures','thumbnailsize');
  	$s = explode('x',$size);
  	$w = (int)$s[0];
  	$h = (int)$s[1];
  	return ($h + 23);
}

/**
 * This function switches the value of two given positions
 * in a given array
 *
 * @param 	$array	array
 * @param	$pos1	int
 * @param 	$pos2	int
 * @return 	array
 */
function switchArrayElements($array,$pos1,$pos2) {
    $cache = $array[$pos1];
    $array[$pos1] = $array[$pos2];
    $array[$pos2] = $cache;
    return $array;
}

?>