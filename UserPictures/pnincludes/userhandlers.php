<?php

class UserPictures_user_SettingsHandler
{
  	var $uid;
	function initialize(&$render)
	{	    
	  	// Admins should be able to modify user's profile data
	  	$this->uid = pnUserGetVar('uid');
		$data = pnModAPIFunc('UserPictures','user','getSettings',array('uid'=>$this->uid));
		$render->assign($data);
		return true;
    }
	function handleCommand(&$render, &$args)
	{
		if ($args['commandName']=='update') {
		    // Security check 
		    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();		    
		    if (!$render->pnFormIsValid()) return false;
		    $obj['uid'] = $this->uid;
			if (pnModAPIFunc('UserPictures','user','setSettings',$obj)) {
			  	LogUtil::registerStatus(_USERPICTURESSETTINGSSTORED);
			  	return true;
			}
			else return false;
		}
		return true;
    }
}
?>