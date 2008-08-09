<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

// Load handlers
Loader::requireOnce('modules/UserPictures/pnincludes/userhandlers.php');

/**
 * the main user function
 *
 * This function returns the main user output and links
 * to the picture management page
 * 
 * @return       output       The main module page
 */
function UserPictures_user_main()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

    // Create output, assign data and return output
    $render = pnRender::getInstance('UserPictures');    
    
    // assign data
    $render->assign('templates',		pnModAPIFunc('UserPictures','admin','getTemplates'));
    $render->assign('globalcategories',	pnModAPIFunc('UserPictures','admin','getGlobalCategory'));
    $render->assign('ownuploads',		pnModGetVar('UserPictures','ownuploads'));
    $render->assign('viewer_uid',		pnUserGetVar('uid'));
    $render->assign('avatarmanagement',	pnModGetVar('UserPictures','avatarmanagement'));
    
    // return output
    return $render->fetch('userpictures_user_main.htm');
}

/**
 * manage avatar picture
 *
 * this function povides the functionallity to the user to 
 * manage the global avatar image
 *
 * @return	output
 */
function UserPictures_user_avatar()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

	// check for action
	$action = FormUtil::getPassedValue('action');
	if (isset($action) && ($action == "del")) {
		if (SecurityUtil::confirmAuthKey()) {
		  	pnUserSetVar('_YOURAVATAR','blank.gif');
		  	LogUtil::registerStatus(_USERPICTURESAVATARREMOVED);
		}
		else LogUtil::registerAuthIDError();
	  	return pnRedirect(pnModURL('UserPictures','user','avatar'));
	}

	// create output
	$render = FormUtil::newpnForm('UserPictures');
    return $render->pnFormExecute('userpictures_user_avatar.htm', new UserPictures_user_AvatarHandler());
}

/**
 * view function
 *
 * show pictures of a user's gallery
 *
 * @param	$args['uid']			user id
 * @param	$args['template_id']	template_id
 * @return	output
 */
function UserPictures_user_view() 
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

	// check action: delete association
	$delassoc = FormUtil::getPassedValue('delassoc');
	if (isset($delassoc) && ($delassoc > 0)) {
	 	if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
	 	else {
	 	  	// get picture id and picture for redirect
	 	  	$obj		= DBUtil::selectObjectByID('userpictures_persons',$delassoc);
	 	  	$id 		= $obj['picture_id'];
	 	  	print "id $id";
			$pictures	= pnModAPIFunc('UserPicture','user','get',array('id' => $id));
			$picture	= $pictures[0];
			die(prayer($pictures));
	 	  	// now delete
		   	if (pnModAPIFunc('UserPictures','user','delPerson',array ('id' => $delassoc))) LogUtil::registerStatus(_USERPICTURESASSOCDELETED);
			else LogUtil::registerError(_USERPICTURESERRORDELETINGASSOC);
			return pnRedirect($picture['url'].'&managepicture=1&singlemode=1');
		}
	}
	// create output object
	$render = FormUtil::newpnForm('UserPictures');
    return $render->pnFormExecute('userpictures_user_view.htm', new UserPictures_user_ViewHandler());
}

/**
 * user's settings
 *
 * Users can manage their personal UserPicture settings here
 * 
 * @return       output      
 */
function UserPictures_user_settings()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

	// is there any action to do?
    $action=FormUtil::getPassedValue('action','');
	if ($action=="deleteMyLinks") {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();	// auth-key check
		else {
		    // Now we should delete all links from all images to a given person
		    if (pnModAPIFunc('UserPictures','user','delPerson',array('uname'=>pnUserGetVar('uname')))) LogUtil::registerStatus(_USERPICTURESASSOCSDELETED);
		}
    }

	// Create output and assign data
	$render = FormUtil::newpnForm('UserPictures');
    return $render->pnFormExecute('userpictures_user_settings.htm', new UserPictures_user_SettingsHandler());
}

/**
 * manage categories
 *
 * This function display the category management for a user's gallery
 * 
 * @return       output       
 */
function UserPictures_user_manageCategories()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $pnRender = pnRender::getInstance('UserPictures');

	// get variables    
    $action 	= FormUtil::getPassedValue('action','');
    $uid		= pnUserGetVar('uid');
    $categories = pnModAPIFunc('UserPictures','user','getCategory',array(	'uid'	=> $uid));
    $pnRender->assign('uid',		$uid);
    $pnRender->assign('categories',	$categories);

	// check for requested action
    if ($action == 'newcat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $text	= FormUtil::getPassedValue('text','');
		    $title	= FormUtil::getPassedValue('title','');
		    if (pnModAPIFunc('UserPictures','user','addCategory',array(	'uid'	=> $uid,
																		'title'	=> $title,
																		'text'	=> $text))) LogUtil::registerStatus(_USERPICTURESCATCREATED);
		    else LogUtil::registerError(_USERPICTURESADDCATFAILED);
		}
    }
    else if ($action == 'editcat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $text	= FormUtil::getPassedValue('text','');
		    $id		= FormUtil::getPassedValue('id');
		    $title	= FormUtil::getPassedValue('title');
		    $delete	= FormUtil::getPassedValue('delete');
		    if (pnModAPIFunc('UserPictures','user','editCategory',array(	'id'		=> $id,
																			'title'		=> $title,
																			'text'		=> $text,
																			'uid'		=> $uid,
																			'delete'	=> $delete))) LogUtil::registerStatus(_USERPICTURESACTIONDONE);
		    else LogUtil::registerError(_USERPICTURESEDITFAILED);
		}
    }
    else if ($action == 'addtocat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $picture_id = (int)FOrmUtil::getPassedValue('picture_id');
		    $cat_id 	= (int)FormUtil::getPassedValue('cat_id');
		    if (pnModAPIFunc('UserPictures','user','addToCategory',array(	'uid'			=> $uid,
																			'picture_id' 	=> $picture_id,
																			'cat_id'		=> $cat_id))) {
		        LogUtil::registerStatus(_USERPICTURESADDEDTOCATEGORY);
		        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'	=> 0)).'#'.$picture_id);
		    }
		    else {
		        LogUtil::registerError(_USERPICTURESADDTOCATFAILURE);
		        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'	=> 0)));
		    }
		}
    }
    else if ($action == 'delfromcat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $picture_id	= (int)FormUtil::getPassedValue('picture_id');
		    $cat_id 	= (int)FormUtil::getPassedValue('cat_id');
		    if (pnModAPIFunc('UserPictures','user','delFromCategory',array(	'uid'			=> $uid,
																			'picture_id'	=> $picture_id,
																			'cat_id'		=> $cat_id))) {
		        LogUtil::registerStatus(_USERPICTURESDELETEDFROMCATEGORY);
		        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'	=> 0)).'#'.$picture_id);
		    }
    	    else {
		        LogUtil::registerError(_USERPICTURESDELETEFROMCATFAILURE);
			return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'	=> 0)));
		    }
		}
    }

    // clean the url
    if ($action!='') return pnRedirect(pnModURL('UserPictures','user','manageCategories'));

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_managecategories.htm');
}

/**
 * manage pictures
 *
 * This function provides the picture management of templates
 * and of the "own gallery" of each user
 * 
 * @return       output       
 */
function UserPictures_user_managePicture()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) {
	  	LogUtil::registerPermissionError();
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }

	// is userpictures module activated?
    $activated = pnModGetVar('UserPictures','activated');
    if ($activated != 1) {
		$disabledtext = pnModGetVar('UserPictures','disabledtext');
		if ($disabledtest != '') $addon = ': '.$disabledtext;
	  	LogUtil::registerError(_USERPICTURESDISABLED.' '.$addon);
	  	return pnRedirect(pnModURL('Userpictures','user','main'));
	}

	// get some passed values	
    $action 		= FormUtil::getPassedValue('action');
    $picture_id 	= FormUtil::getPassedValue('picture_id');
    $uid 			= pnUserGetVar('uid');
    $cat_id			= FormUtil::getPassedValue('cat_id',0);
    $template_id 	= FormUtil::getPassedValue('template_id');
    // check auth key first
    if (isset($action) && (strlen($action)>0)) {
		// is the auth-key correct?
		if (!SecurityUtil::confirmAuthKey()) {
		  	LogUtil::registerAuthIDError();
	        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
	    }
	}
	// check for requested action
	switch ($action) {
	  	case "delete":
			if (pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$picture_id,'uid'=>$uid,'template_id'=>$template_id))) LogUtil::registerStatus(_USERPICTURESDELETED);
			else LogUtil::registerError(_USERPICTURESDELETEERROR);
			break;
		case "addtocat":
			if (pnModAPIFunc('UserPictures','user','setCategory',array('picture_id'=>$picture_id,'cat_id' => $cat_id, 'uid' => $uid))) LogUtil::registerStatus(_USERPICTURESADDEDTOCAT);
			else LogUtil::registerError(_USERPICTURESERRORADDINGTOCAT);
			break;
		case "delfromcat":
			if (pnModAPIFunc('UserPictures','user','setCategory',array('picture_id'=>$picture_id,'uid' => $uid,'cat_id' => 0))) LogUtil::registerStatus(_USERPICTURESCATASSOCDELETED);
			else LogUtil::registerError(_USERPICTURESERRORDELETINGCATASSOC);
			break;
		case "addtoglobalcat":
			if (pnModAPIFunc('UserPictures','user','setGlobalCategory',array('picture_id'=>$picture_id,'cat_id' => $cat_id, 'uid' => $uid))) LogUtil::registerStatus(_USERPICTURESADDEDTOGLOBALCAT);
			else LogUtil::registerError(_USERPICTURESERRORADDINGTOGLOBALCAT);
			break;
		case "delfromglobalassoc":
			if (pnModAPIFunc('UserPictures','user','setGlobalCategory',array('picture_id'=>$picture_id,'uid' => $uid,'cat_id' => 0))) LogUtil::registerStatus(_USERPICTURESGLOBALCATASSOCDELETED);
			else LogUtil::registerError(_USERPICTURESERRORDELETINGGLOBALCATASSOC);
			break;
		case "setcomment":
			$comment = pnVarCleanFromInput('comment');
			if (pnModAPIFunc('UserPictures','user','setComment',array('picture_id'=>$picture_id,'uid'=>$uid,'comment'=>$comment))) LogUtil::registerStatus(_USERPICTURECOMMENTCHANGED);
			else LogUtil::registerError(_USERPICTURESCOMMENTCHANGEERROR);
			break;
		case "rotate":
			if (pnModAPIFunc('UserPictures','user','rotatePicture',array('angle'=>pnVarCleanFromInput('angle'),'uid'=>pnUserGetVar('uid'),'template_id'=>$template_id,'picture_id'=>$picture_id)  ) ) LogUtil::registerStatus(_USERPICTURESROTATED);
			else LogUtil::registerError(_USERPICTURESROTATEDERROR);
			break;
		case "upload":
			// check if the user's picture limit is alread reached
			$pictures = pnModAPIFunc('UserPictures','user','getPictures',array('uid'=>$uid,'template_id'=>$template_id));
			if (count($pictures)>= pnModGetVar('UserPictures','ownuploads') && ($template_id == 0)) {
			    LogUtil::registerError(_USERPICTURESUPLOADLIMITREACHED);
		        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			}
			else if ((count($pictures)> 0) && ($template_id!=0)) {
			    LogUtil::registerError(_USERPICTURESUPLOADLIMITREACHED);
		        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			}
			
			// now we will handle the upload
			$res = pnModAPIFunc('UserPictures','user','handleUploadedPicture',array('uid'=>$uid,'template_id'=>$template_id));
			switch ($res) {
			  case 1:
				LogUtil::registerStatus(_USERPICTURESUPLOADED);
			  	break;
			  case 2:
	            LogUtil::registerError(_USERPICTURESWRONGFILEEXTENSION);
	            return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			  	break;
			  case 3:
			    LogUtil::registerError(_USERPICTURESMAFILESIZEREACHED);
	            return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			  	break;
			  case 4:
			    LogUtil::registerError(_USERPICTURESUPLOADERROR);
			    return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			  	break;
			  default:
			    LogUtil::registerError(_USERPICTURESERRORUPLOADING."-$res");
			    return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
			  	break;
			}
			break;
	}

    // this is just to clean the browsers url input field and to 
    // avoid errors caused by navigating with the browser buttons
    if ($template_id == 0) $linkname = "#$picture_id";
    if ($action != '') return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)).$linkname);

    // Create regular output - no action was to be done
    $render = pnRender::getInstance('UserPictures');

    // Get template information
    $template = pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$template_id));
    if (!($template[id]>=0)) {
		LogUtil::registerError(_USERPICTURESTEMPLATENUMBERFALSE);
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }
    else $template_id = $template['id'];

    // Assign some values to some variables
	$pictures = pnModAPIFunc('UserPictures','user','get',array (
			'uid' 			=> $uid,
			'template_id' 	=> $template_id
		));

	// Add order link to make position changes possible if javascript is disabled
	if (count($pictures) > 0) $pictures = pnModAPIFunc('UserPictures','user','addOrderLinkToPictures',array('pictures' => $pictures));
	if ($template_id == 0) {
	    $globalCategories 	= pnModAPIFunc('UserPictures','admin','getGlobalCategory');
	    $categories 		= pnModAPIFunc('UserPictures','user','getCategory',array('uid'=>$uid));
	    if (count($globalCategories)>0)		$render->assign('globalcategories',$globalCategories);
	    if (count($categories)>0) 			$render->assign('categories',$categories);
		$render->assign('globalcategories',	pnModAPIFunc('UserPictures','admin','getGlobalCategory'));
		$render->assign('ajaxurl',			pnGetBaseUrl().pnModURL('UserPictures','ajax','ajaxSaveList'));
	}
    $render->assign('uid',				pnUserGetVar('uid'));
    $render->assign('ownuploads',		pnModGetVar('UserPictures','ownuploads'));
    $render->assign('verifytext',		pnModGetVar('UserPictures','verifytext'));
    $render->assign('avatarmanagement',	pnModGetVar('UserPictures','avatarmanagement'));
    $render->assign('template',			$template);
	$render->assign('pictures',			$pictures);
	$render->assign('authid',			SecurityUtil::generateAuthKey());

    // Return the output that has been generated by this function
    return $render->fetch('userpictures_user_managepicture.htm');
}

/**
 * save list (noscript)
 * 
 * noscript tp ajax call to store the new field-list
 *
 * @return	output
 */
function UserPictures_user_saveList()
{
	$order = unserialize(FormUtil::getPassedValue('order'));
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();
 	// store the new order    
	pnModAPIFunc('UserPictures','ajax','ajaxSaveList',array('list' => $order));
    return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id' => FormUtil::getPassedValue('template_id'))));
}
?>