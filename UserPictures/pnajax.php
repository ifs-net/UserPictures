<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * ajax call to store the new field-list
 *
 * @return	output
 */
function UserPictures_ajax_ajaxSaveList()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();
	// store the new order    
	pnModAPIFunc('UserPictures','user','ajaxSaveList',array('list'=> FormUtil::getPassedValue('userpictures_list')));
    return true;
}
?>