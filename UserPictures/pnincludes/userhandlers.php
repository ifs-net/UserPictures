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
 * handler for avatar management
 */
class UserPictures_user_AvatarHandler
{
	function initialize(&$render)
	{	    
	  	// Assign the uploaded pictures
	  	$pictures = pnModAPIFunc('UserPictures','user','get',array('uid' => pnUserGetVar('uid')));
	  	if (!(count($pictures) > 0)) {
	  	  	LogUtil::registerError(_USERPICTURESUPLOADPICFIRST);
		    return pnRedirect(pnModURL('UserPictures','user','main'));
		}
		// assign data
		$render->assign('pictures',		$pictures);
		$render->assign('timestamp',	time());
		$render->assign('authid',		SecurityUtil::generateAuthKey());
		$render->assign('avatar', 		pnModGetVar('UserPictures','avatardir').'/'.pnUserGetVar('_YOURAVATAR',pnUserGetVar('uid')));
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
			// set image as avatar
			if (pnModAPIFunc('UserPictures','user','setAvatar',array(
				'picture_id'	=> $obj['picture_id'],
				'uid'			=> pnUserGetVar('uid')))) {
				LogUtil::registerStatus(_USERPICTURESSETASAVATAR);
				return pnRedirect(pnModURL('UserPictures','user','avatar'));
			} // else...
			LogUtil::registerError(_USERPICTURESSETASAVATARERROR);
		    return false;
		}
		return true;
    }
}

/**
 * handler for user settings management
 */
class UserPictures_user_SettingsHandler
{
  	var $uid;
	function initialize(&$render)
	{	    
	  	// Admins should be able to modify user's profile data
	  	$this->uid = pnUserGetVar('uid');
		$render->assign('ezcomments', pnModAvailable('EZComments'));
		$render->assign(pnModAPIFunc('UserPictures','user','getSettings',array('uid'=>$this->uid)));
		$render->assign(pnModAPIFunc('UserPictures','user','getSettings'));
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

/**
 * handler for associate person to pictures management
 */
class UserPictures_user_ViewHandler
{
  	var $id;
  	var $viewurl;
	function initialize(&$render)
	{	    
	    // get parameters
	    $uid				= (int)	FormUtil::getPassedValue('uid');
	    $assoc_uid			= (int)	FormUtil::getPassedValue('assoc_uid');
	    $template_id		= 		FormUtil::getPassedValue('template_id');
	    $picture_id			= 		FormUtil::getPassedValue('id');
	    $cat_id				= (int)	FormUtil::getPassedValue('cat_id');
	    $globalcat_id		= (int)	FormUtil::getPassedValue('globalcat_id');
	    $startwith			= (int)	FormUtil::getPassedValue('upstartwith');
	    $managepicturelink	= (int)	FormUtil::getPassedValue('managepicturelink');
	    $singlemode			= 		FormUtil::getPassedValue('singlemode');
	    if ($picture_id > 0) 	$singlemode 	= 1;
	    if (!($startwith > 0)) 	$startwith 		= 0;
	    if ($singlemode > 0)	$showmax 		= 1;
	    else					$showmax 		= 20;	// Todo: replace with module variable

	    // get pictures
	    $pictures = pnModAPIFunc('UserPictures','user','get',array (
	    		'uid'				=> $uid,
	    		'assoc_uid'			=> $assoc_uid,
	    		'template_id'		=> $template_id,
	    		'cat_id'			=> $cat_id,
	    		'id'				=> $picture_id,
	    		'globalcat_id'		=> $globalcat_id,
	    		'startwith'			=> $startwith,
	    		'showmax'			=> $showmax,
	    		'managepicturelink'	=> 1,
	    		'expand'			=> true
			));
	
		// get number of pictures for counter
	    $pictures_count = pnModAPIFunc('UserPictures','user','get',array (
	    		'uid'				=> $uid,
	    		'assoc_uid'			=> $assoc_uid,
	    		'template_id'		=> $template_id,
	    		'id'				=> $picture_id,
	    		'cat_id'			=> $cat_id,
	    		'globalcat_id'		=> $globalcat_id,
	    		'countonly'			=> true
			));

		// Add overlib
	    PageUtil::addVar('javascript','javascript/overlib/overlib.js');

		// assign data
		if ($uid > 1) 				$render->assign('uid',	$uid);
		else if ($assoc_uid > 1) 	$render->assign('uid',	$assoc_uid);
		$render->assign('pictures_count',		$pictures_count);
		$render->assign('pictures',				$pictures);
		$render->assign('picture_id',			$picture_id);	// we need this for a managepicture backlink!
		$render->assign('thumbnailheight',		up_getThumbnailHeight());
		$render->assign('no_uname',				(($uid > 1) || ($assoc_uic > 1)));
		$render->assign('showmax',				$showmax);
		$render->assign('ezcommentsavailable',	(pnModAvailable('EZComments') && pnModIsHooked('EZComments','UserPictures')));
		$render->assign('managepicturelink',	$managepicturelink);
		$render->assign('viewer_uid',			pnUserGetVar('uid'));
		if ($singlemode > 0) {
		  	// assign viewurl for EZComments integration and persons associations
		  	$viewurl = pnModURL('UserPictures','user','view',array(
	    		'uid'				=> $uid,
	    		'assoc_uid'			=> $assoc_uid,
	    		'template_id'		=> $template_id,
	    		'cat_id'			=> $cat_id,
	    		'globalcat_id'		=> $globalcat_id,
	    		'upstartwith'		=> $startwith,
	    		'showmax'			=> $showmax,
	    		'managepicturelink'	=> $managepicturelink,
	    		'singlemode'		=> 1
			  	));
		  	if (!($picture_id > 0))$viewthumbs = pnModURL('UserPictures','user','view',array(
	    		'uid'				=> $uid,
	    		'assoc_uid'			=> $assoc_uid,
	    		'template_id'		=> $template_id,
	    		'cat_id'			=> $cat_id,
	    		'globalcat_id'		=> $globalcat_id,
	    		'managepicturelink'	=> $managepicturelink,
	    		'upstartwith'		=> 1
			  	));
		  	$render->assign('authid',		SecurityUtil::generateAuthKey());
		  	$render->assign('viewthumbs',	$viewthumbs);
			$p 				= $pictures[0];
			$render->assign('redirect',		base64_encode($p['url']));
			$this->viewurl	= $p['url'];
			$this->id 		= $p['id'];
		}
		// if there is a user gallery to be displayed, we'll have to assign the user's private categories
		if ($uid > 0) {
		  	$render->assign('categories',pnModAPIFunc('UserPictures','user','getCategory',array('uid' => $uid)));
		  	if ($cat_id > 0) $render->assign('category',pnModAPIFunc('UserPictures','user','getCategory',array('cat_id'=>$cat_id)));
		}
		
		return true;
    }
	function handleCommand(&$render, &$args)
	{
		if ($args['commandName']=='add') {
		    // Security check - only registered users can associate persons
		    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT) || (!(pnUserGetVar('uid') > 1))) return LogUtil::registerPermissionError();

			// get the pnForm data and do a validation check
		    $obj = $render->pnFormGetValues();		    
		    if (!$render->pnFormIsValid()) return false;
			$id = $this->id;

			// does the username exist?
			$assoc_uid 	= pnUserGetIDFromName($obj['uname']);
			if (!($assoc_uid > 0)) {
			  	LogUtil::registerError(_USERPICTUREUNAMENOTFOUND);
			  	return false;
			}
			$uname 		= pnUserGetVar('uname',$assoc_uid);
			
			// no linkine allowed because of user settings or contactlist's ignore list?
			$settings 	= pnModAPIFunc('UserPictures','user','getSettings',array('uid' => $assoc_uid));
			if (($settings['nolinking'] == 1) || (pnModAvailable('ContactList') && pnModAPIFunc('ContactList','user','isIgnored',array (
				'uid' 	=> $assoc_uid,
				'iuid' 	=> pnUserGetVar('uid'))))) {
			  	LogUtil::registerError(_USERPICTURESNOLINKINGFORUSER);
			  	return false;
			}
						
			// get some db information
		    $tables =& pnDBGetTables();
		    $personscolumn 				= &$tables['userpictures_persons_column'];

			// Get picture object
			$pic = DBUtil::selectObjectByID('userpictures',$id);
			if (!($pic['id'] > 0 )) return false;	// something went wrong fetching the picture

			print "id ist $id";
			// get existing association
			$where 		= $personscolumn['assoc_uid']." = ".$assoc_uid." AND ".$personscolumn['picture_id']." = ".$id;
			$assocs 	= DBUtil::selectObjectArray('userpictures_persons',$where);
			if (count($assocs) != 0) {
			  	LogUtil::registerError(_USERPICTURESUSERALREADYADDED);
			  	return false;
			}

			// add user to database and process result
			$obj = array (	'uid'			=> pnUserGetVar('uid'),
							'picture_id'	=> $id,
							'assoc_uid'		=> $assoc_uid		);
			if (DBUtil::insertObject($obj,'userpictures_persons')) {
			  	LogUtil::registerStatus(_USERPICTURESPERSONADDED);
			  	// send an email with "you were associated to a photo"-text
			  	$uid 	= pnUserGetVar('uid');
	            $render = pnRender::getInstance('UserPictures');
	            $render->assign('sitename',		pnConfigGetVar('sitename'));
	            $render->assign('uid',			$uid);
	            $render->assign('assoc_uid',	$assoc_uid);
	            $render->assign('uname',		pnUserGetVar('uname',$uid));
	            $render->assign('assoc_uname',	pnUserGetVar('uname',$assoc_uid));
	            $render->assign('viewurl',		pnGetBaseURL().pnModURL('UserPictures','user','view',array('id' => $id)));
	            $body = $render->fetch('userpictures_email_youwereadded.htm');
	            $subject = _USERPICTURESYOUWEREFOUNDATPIC;
	            if ($assoc_uid != $uid) pnMail(pnUserGetVar('email',$assoc_uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
			  	
			}
			return pnRedirect(pnGetBaseURL().$this->viewurl);
		}
		return true;
    }
}
?>