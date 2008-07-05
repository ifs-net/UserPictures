<?php

/**
 * ajax call to store the new field-list
 *
 * @return	output
 */
function UserPictures_ajax_ajaxSaveList()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();
	// store the new order    
	pnModAPIFunc('UserPictures','user','ajaxSaveList',array('list'=> FormUtil::getPassedValue('userpictures_list')));
    return true;
}
?>