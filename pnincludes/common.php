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
 * add some page vars for up output
 *
 * @return	void
 */
function up_addPageVars()
{
    // Add some page vars
    // Due to some conflicts with lightbox we cannot use the protoype and 
	// scriptaculous versions that are distributed with zikula
    PageUtil::addVar('stylesheet','modules/UserPictures/pnincludes/lightbox/css/lightbox.css');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/prototype.js');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/scriptaculous/src/scriptaculous.js?load=effects,builder,dragdrop');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/lightbox.js');
    PageUtil::addVar('javascript','javascript/overlib/overlib.js');
	return true;
}

/**
 * build where string for sql statement
 *
 * @param	array
 * @result	string
 */
function up_constructWhere($whereArray)
{
  	// construct where statement for sql	
  	$first = true;
  	unset($where);
	foreach ($whereArray as $a)	{
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
?>