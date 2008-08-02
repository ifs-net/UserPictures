<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schie�l
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
    $render->assign('startnumthumb',$startnumthumb);
    return $render->fetch('userpictures_user_main.htm');
}

/**
 * show pictures
 *
 * @return       output       
 */
function UserPictures_user_show()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

	pnModAPIFunc('UserPictures','user','get',array(
		'uid' => pnUserGetVar('uid'),
		'cat_id' => 5
		));

    // Create output, assign data and return output
    $render = pnRender::getInstance('UserPictures');    
    $render->assign('startnumthumb',$startnumthumb);
    return $render->fetch('userpictures_user_main.htm');
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
 * associate persons with pictures
 *
 * This function can associate pictures and persons
 * to make the galleries more "social"
 * 
 * @return       output       The main module page
 */
function UserPictures_user_assocPersons()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $pnRender = pnRender::getInstance('UserPictures');
    
    $pictures=pnModAPIFunc('UserPictures','user','getPicture',array('picture_id'=>pnVarCleanFromInput('picture_id')));
    $picture=$pictures[0];
    if ($picture[uid] == pnUserGetVar('uid')) $pnRender->assign('picture',$picture);
    $picture_id=$picture[id];
    
    // now we have to check for any action
    $action = FormUtil::getPassedValue('action','');
    if ($action == 'add') {
	// is the auth-key correct?
		if (!SecurityUtil::confirmAuthKey()) {
		  	LogUtil::registerAuthIDError();
            return pnRedirect(pnModURL('UserPictures','user','assocPersons',array('picture_id'=>$picture_id)));
        }
		$uname=FormUtil::getPassedValue('uname','');
		if (!(strlen($uname)>0)) logUtil::registerErro(_USERPICTURESNOCONTACTNAMEGIVEN);
		else if (pnModAPIFunc('UserPictures','user','addPerson',array(	'picture_id'	=> $picture_id,
																		'uname'			=> $uname))) LogUtil::registerStatus(_USERPICTURESCONTACTADDED);
		else pnSessionSetVar('errormsg',_USERPICTURESUSERNOTFOUNDORADDED);
	    }
	    if ($action == 'delete') {
		// is the auth-key correct?
	        if (!pnSecConfirmAuthKey()) {
	            pnSessionSetVar('errormsg', _BADAUTHKEY);
	            return pnRedirect(pnModURL('UserPictures','user','assocPersons',array('picture_id'=>$picture_id)));
	        }
		$uname=FormUtil::getPassedValue('uname','');
		if (!(strlen($uname)>0)) LogUtil::registerError(_USERPICTURESNOCONTACTNAMEGIVEN);
		else if (pnModAPIFunc('UserPictures','user','delPerson',array(	'picture_id'	=>$picture_id,
																		'uname'=>$uname))) LogUtil::registerStatus(_USERPICTURESCONTACTDELETED);
		else LogUtil::registerError(_USERPICTURESDELETEERROR);
    }
    
    if ($action != '') return pnRedirect(pnModURL('UserPictures','user','managePicture',array(	'template_id'	=> $picture['template_id']))."#".$picture[id]);
    
    $pnRender->assign('assocPersons',pnModAPIFunc('UserPictures','user','getPersons',array(	'picture_id'	=> $picture_id)));
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_assocpersons.htm');
}

/**
 * latest images
 *
 * This function shows the latest uploads of all users
 * 
 * @return       output       The main module page
 */
function UserPictures_user_latest()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

    // Create output 
    $pnRender = pnRender::getInstance('UserPictures');
    
    $startnumthumb = FormUtil::getPassedValue('startnumthumb',1);
    if (!($startnumthumb>0)) $startnumthumb = 1;
    
    $pnRender->assign('startnumthumb',$startnumthumb);
	    
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_latest.htm');
}

/**
 * associated images
 *
 * This function shows images that are associated with a given user
 * 
 * @param	$args['uid']	int
 * @return       output     
 */
function UserPictures_user_assoc()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

    // Create output 
    $pnRender = pnRender::getInstance('UserPictures');
    $uid = FormUtil::getPassedValue('uid',0);
    $pnRender->assign('uid',$uid);
    
    $startnumthumb = FormUtil::getPassedValue('startnumthumb',1);
    if (!($startnumthumb>0)) $startnumthumb = 1;
    $pnRender->assign('startnumthumb',$startnumthumb);
    
    $hideown = FormUtil::getPassedValue('hideown',0);
    if ($hideown==1) $pnRender->assign('hideown','1');
	    
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_assoc.htm');
}

/**
 * show gallery
 *
 * This function shows a user's gallery
 * 
 * @return       output       The main module page
 */
function UserPictures_user_showGallery()
{
  	// security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();
    
    $uid=FormUtil::getPassedValue('uid',0);
    if (!($uid>0)) LogUtil::registerError(_USERPICTURESNOUSERSPECIFIED);

    // Security check removed - we will use our own security check ;-)
    $settings=pnModAPIFunc('UserPictures','user','getSettings',array('uid'	=> $uid));
    if (($settings['picspublic']!=1) && (!pnUserLoggedIn())) return LogUtil::registerPermissionError();

    // Create output object
    $pnRender = pnRender::getInstance('UserPictures');
    
    $pnRender->assign('uid',$uid);
    $pnRender->assign('viewer_uid',pnUsergetVar('uid'));
    
    // is there a category filter active?
    $cat_id=(int)FormUtil::getPassedValue('cat_id');
    $pnRender->assign('cat_id',$cat_id);
    
    // we need the user's settings
    $pnRender->assign('settings',pnModAPIFunc('UserPictures','user','getSettings',array('uid'	=> $uid)));
    
    // we need the pager's value
    $startnum=FormUtil::getPassedValue('startnum');
    if (!($startnum > 0)) $startnum = 1;

    $pictures=pnModAPIFunc('UserPictures','user','getPictures',array(	'template_id'	=> 0,
																		'uid'			=> $uid,
																		'cat_id'		=> $cat_id));
    $pictures_startnum=pnModAPIFunc('UserPictures','user','getPictures',array(	'template_id'	=> 0,
																				'uid'			=> $uid,
																				'cat_id'		=> $cat_id,
																				'startnum'		=> $startnum));

    $pnRender->assign('pictures',$pictures_startnum);
    $pnRender->assign('picturecounter',count($pictures));
    $startnum=pnVarCleanFromInput('startnum');
    if (!($startnum>=0)) return pnRedirect(pnModURL('UserPictures','user','showThumbnailGallery',array(	'uid'		=> $uid,
																										'cat_id'	=> $cat_id)));
    $pnRender->assign('startnum',$startnum);

    // we need to check if the pic_id is correct. otherwise we'll create a new redirect. this is important for the use of a comment module for example!
    // the pager link might be incorrenct!
    $pic_id=(int)FormUtil::getPassedValue('pic_id');
    if ($pic_id!=$pictures_startnum[0]['id']) return pnRedirect(pnModURL('UserPictures','user','showGallery',array('uid'=>pnVarCleanFromInput('uid'),'cat_id'=>pnVarCleanFromInput('cat_id'),'pic_id'=>$pictures_startnum[0]['id'],'startnum'=>pnVarCleanFromInput('startnum'))));

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_showgallery.htm');
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
    
    $uid=pnUserGetVar('uid');
    $pnRender->assign('uid',$uid);
    $categories = pnModAPIFunc('UserPictures','user','getCategories',array(	'uid'	=> $uid));
    $pnRender->assign('categories',$categories);

    $action = FormUtil::getPassedValue('action','');

    if ($action == 'newcat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $text=FormUtil::getPassedValue('text','');
		    $title=FormUtil::getPassedValue('title','');
		    if (pnModAPIFunc('UserPictures','user','addCategory',array(	'uid'	=> $uid,
																		'title'	=> $title,
																		'text'	=> $text))) LogUtil::registerStatus(_USERPICTURESCATCREATED);
		    else LogUtil::registerError(_USERPICTURESADDCATFAILED);
		}
    }
    else if ($action == 'editcat') {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $text=FormUtil::getPassedValue('text','');
		    $id=FormUtil::getPassedValue('id');
		    $title=FormUtil::getPassedValue('title');
		    $delete=FormUtil::getPassedValue('delete');
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
		    $cat_id = (int)FormUtil::getPassedValue('cat_id');
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
		    $picture_id = (int)FormUtil::getPassedValue('picture_id');
		    $cat_id = (int)FormUtil::getPassedValue('cat_id');
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
 * show thumbnail gallery
 *
 * This function displays a user's gallery in thumbnail mode
 * 
 * @return       output       
 */
function UserPictures_user_showThumbnailGallery()
{
  	// securtiy check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

    $uid=FormUtil::getPassedValue('uid',0);
    if (!($uid>0)) {
		LogUtil::registerError(_USERPICTURESNOUSERSPECIFIED);
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }

    // Security check removed - we will use our own security check ;-)
    $settings=pnModAPIFunc('UserPictures','user','getSettings',array('uid'	=> $uid));
    if (($settings['picspublic']!=1) && (!pnUserLoggedIn())) {
        return LogUtil::registerPermissionError();
    }


    // Create output
    $pnRender = pnRender::getInstance('UserPictures');

    // pager
    $startnumthumb = FormUtil::getPassedValue('startnumthumb',1);
    if (!isset($startnumthumb) || (!($startnumthumb>0))) $startnumthumb = 1;
    $pnRender->assign('startnumthumb',	$startnumthumb);
    $pnRender->assign('uid',			$uid);
    $pnRender->assign('viewer_uid',		pnUsergetVar('uid'));

    // there might be a category filter active
    $cat_id=(int)FormUtil::getPassedValue('cat_id');
    $pnRender->assign('cat_id',$cat_id);
    // let's get the pictures
    $pictures = pnModAPIFunc('UserPictures','user','getPictures',array(	'template_id'	=> 0,
																		'uid'			=> $uid,
																		'cat_id'		=> $cat_id));
    $picturesselected = pnModAPIFunc('UserPictures','user','getPictures',array(	'template_id'	=> 0,
																				'uid'			=> $uid,
																				'cat_id'		=> $cat_id,
																				'startnumthumb'	=> $startnumthumb));
    $pnRender->assign('picturesselected',	$picturesselected);
    $pnRender->assign('pictures',			$pictures);
    $diffPics = pnModAPIFunc('UserPictures','user','picturesDiff',array(	'allPics'	=> $pictures,
																			'shownPics'	=> $picturesselected));
	$pnRender->assign('diffpics',			$diffPics);
    $pnRender->assign('picturecounter',		count($pictures));
    $startnum = FormUtil::getPassedValue('startnum',1);
    if (!isset($startnum) or (!($startnum>=0))) $startnum=1;
    $pnRender->assign('startnum',			$startnum);
    
    // Add some page vars
    PageUtil::addVar('stylesheet','modules/UserPictures/pnincludes/lightbox/css/lightbox.css');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/prototype.js');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/scriptaculous.js?load=effects,builder');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/lightbox.js');
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_user_showthumbnailgallery.htm');
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
    $template_id 	= FormUtil::getPassedValue('template_id');
    $picture_id 	= FormUtil::getPassedValue('picture_id');
    $uid 			= pnUserGetVar('uid');
    $id 			= FormUtil::getPassedValue('template_id',0);
    $cat_id			= FormUtil::getPassedValue('cat_id',0);
        
    // check auth key first
    if (isset($action) && (strlen($action)>0)) {
		// is the auth-key correct?
		if (!SecurityUtil::confirmAuthKey()) {
		  	LogUtil::registerAuthIDError();
	        return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
	    }
	}
	// now check for actions 
    if (isset($action) && ($action=='delete')) {
		if (pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$picture_id,'uid'=>$uid,'template_id'=>$template_id))) LogUtil::registerStatus(_USERPICTURESDELETED);
		else LogUtil::registerError(_USERPICTURESDELETEERROR);
	}    
    else if (isset($action) && ($action=='avatar')) {
		if (pnModAPIFunc('UserPictures','user','copyPictureAsAvatar',array('picture_id'=>$picture_id,'uid'=>$uid,'template_id'=>$template_id))) LogUtil::registerStatus(_USERPICTURESSETASAVATAR);
		else LogUtil::registerError(_USERPICTURESSETASAVATARERROR);
    }    
    else if (isset($action) && ($action=='addtocat')) {
		if (pnModAPIFunc('UserPictures','user','setCategory',array('picture_id'=>$picture_id,'cat_id' => $cat_id, 'uid' => $uid))) LogUtil::registerStatus(_USERPICTURESADDEDTOCAT);
		else LogUtil::registerError(_USERPICTURESERRORADDINGTOCAT);
    }    
    else if (isset($action) && ($action=='delassoc')) {
		if (pnModAPIFunc('UserPictures','user','setCategory',array('picture_id'=>$picture_id,'uid' => $uid,'cat_id' => 0))) LogUtil::registerStatus(_USERPICTURESCATASSOCDELETED);
		else LogUtil::registerError(_USERPICTURESERRORDELETINGCATASSOC);
    }    
    else if (isset($action) && ($action=='addtoglobalcat')) {
		if (pnModAPIFunc('UserPictures','user','setGlobalCategory',array('picture_id'=>$picture_id,'cat_id' => $cat_id, 'uid' => $uid))) LogUtil::registerStatus(_USERPICTURESADDEDTOGLOBALCAT);
		else LogUtil::registerError(_USERPICTURESERRORADDINGTOGLOBALCAT);
    }    
    else if (isset($action) && ($action=='delglobalassoc')) {
		if (pnModAPIFunc('UserPictures','user','setGlobalCategory',array('picture_id'=>$picture_id,'uid' => $uid,'cat_id' => 0))) LogUtil::registerStatus(_USERPICTURESGLOBALCATASSOCDELETED);
		else LogUtil::registerError(_USERPICTURESERRORDELETINGGLOBALCATASSOC);
    }    
    else if (isset($action) && ($action=='comment')) {
		$comment = pnVarCleanFromInput('comment');
		if (pnModAPIFunc('UserPictures','user','setComment',array('picture_id'=>$picture_id,'uid'=>$uid,'comment'=>$comment))) LogUtil::registerStatus(_USERPICTURECOMMENTCHANGED);
		else LogUtil::registerError(_USERPICTURESCOMMENTCHANGEERROR);
    }    
    else if (isset($action) && ($action=='rotate')) {
		if (pnModAPIFunc('UserPictures','user','rotatePicture',array('angle'=>pnVarCleanFromInput('angle'),'uid'=>pnUserGetVar('uid'),'template_id'=>$template_id,'picture_id'=>$picture_id)  ) ) LogUtil::registerStatus(_USERPICTURESROTATED);
		else LogUtil::registerError(_USERPICTURESROTATEDERROR);
    }
    else if ($action=='upload') {
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
		$res=pnModAPIFunc('UserPictures','user','handleUploadedPicture',array('uid'=>$uid,'template_id'=>$template_id));
		if ($res == 2) {
	            LogUtil::registerError(_USERPICTURESWRONGFILEEXTENSION);
	            return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
		}
		else if ($res == 3) {
		    LogUtil::registerError(_USERPICTURESMAFILESIZEREACHED);
	            return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
	        }
		else if ($res == 3) {
		    LogUtil::registerError(_USERPICTURESUPLOADERROR);
		    return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id)));
		}
		else LogUtil::registerStatus(_USERPICTURESUPLOADED);
    }	

    // this is just to clean the browsers url input field and to 
    // avoid errors caused by navigating with the browser buttons
    if ($action != '') return pnRedirect(pnModURL('UserPictures','user','managePicture',array('template_id'=>$template_id))."#$picture_id");

    // Create regular output - no action was to be done
    $render = pnRender::getInstance('UserPictures');

    // are there global categories?
    $template = pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$id));
    if (!($template[id]>=0)) {
		LogUtil::registerError(_USERPICTURESTEMPLATENUMBERFALSE);
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }

    // Assign some values to some variables
	$pictures = pnModAPIFunc('UserPictures','user','get',array (
			'uid' 			=> $uid,
			'template_id' 	=> $template_id
		));
	if (count($pictures) > 0 ) {
		$pictures = pnModAPIFunc('UserPictures','user','addOrderLinkToPictures',array('pictures' => $pictures));
	}
    $globalCategories = pnModAPIFunc('UserPictures','admin','getGlobalCategory');
    $categories = pnModAPIFunc('UserPictures','user','getCategory',array('uid'=>$uid));
    if (count($globalCategories)>0) $render->assign('globalcategories',$globalCategories);
    if (count($categories)>0) $render->assign('categories',$categories);
	$render->assign('globalcategories',	pnModAPIFunc('UserPictures','admin','getGlobalCategory'));
    $render->assign('uid',				pnUserGetVar('uid'));
    $render->assign('ownuploads',		pnModGetVar('UserPictures','ownuploads'));
    $render->assign('verifytext',		pnModGetVar('UserPictures','verifytext'));
    $render->assign('avatarmanagement',	pnModGetVar('UserPictures','avatarmanagement'));
    $render->assign('template',			$template);
	$render->assign('ajaxurl',			pnGetBaseUrl().pnModURL('UserPictures','ajax','ajaxSaveList'));
	$render->assign('pictures',			$pictures);
    
    // Add some page vars
    PageUtil::addVar('stylesheet','modules/UserPictures/pnincludes/lightbox/css/lightbox.css');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/prototype.js');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/scriptaculous/src/scriptaculous.js?load=effects,builder,dragdrop');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/lightbox.js');

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