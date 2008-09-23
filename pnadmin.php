<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

Loader::includeOnce('modules/UserPictures/pnincludes/adminhandlers.php');

/**
 * main
 *
 * This function shows the main administration page
 *
 * @return       output
 */
function UserPictures_admin_main()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output object
	$render = FormUtil::newpnForm('UserPictures');
    return $render->pnFormExecute('userpictures_admin_main.htm', new userpictures_admin_mainhandler());

}

/**
 * templateToAvatar function
 *
 * This page makes it possible to set actual template image as avatar
 *
 * @return		output
 */
function UserPictures_admin_templateToAvatar()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output object
    $render = pnRender::getInstance('UserPictures');
    
    // Get data
    $templatetoavatar 	= (int)pnModGetVar('UserPictures','templatetoavatar');
    $template 			= pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id' => $templatetoavatar));
    if (($templatetoavatar == 0) || (!($template['id'] > 0))) {
	  	// no template set - we cannnot do anything
	  	LogUtil::registerError(_USERPICTURESSELECTTEMPLATEFIRST);
	  	return pnRedirect(pnModURL('UserPictures','admin','main'));
	}
    
    // Assign data
    $render->assign('template', $template);
    
    // Action? Security Check?
    $action 	= FormUtil::getPassedValue('action');
	$workarray 	= pnSessionGetVar('up_workarray');
    if (isset($action) && (strtolower($action) == 'process')) {
	  	
		// Check auth key
		if (!SecurityUtil::confirmAuthKey()) {
			LogUtil::registerAuthIDError();
		  	return pnRedirect(pnModURL('UserPictures','admin','templatetoavatar'));
		}
		
		// get all pictures of the template. if startwith = 0 (begin the transformation)
		$pictures = pnModAPIFunc('UserPictures','user','get',array('template_id' => $template['id']));
		if (!(isset($workarray) && is_array($workarray))) {
		  	// There is no work array set yet - we'll construct this once for its usage
			$workarray = array();
			foreach($pictures as $picture) {
			  	$uid = $picture['uid'];
			  	$workarray[] = $uid;
			}
			unset($pictures);
			pnSessionSetVar('up_workarray', $workarray);
		}
		
		// no we'll have a workarray
		$c = 0;
		$stop = false;
		$limit = 1;
		while (!$stop) {
		  	$c++;
		  	$next = array_pop($workarray);
		  	// set as avatar
		  	pnModAPIFunc('UserPictures','user','templateToAvatar',
			  	array(	'template_id' 	=> $template['id'], 
				  		'uid' 			=> $next, 
						'no_notice' 	=> 1)	);
		  	// done?
		  	if ((count($workarray) == 0) || ($c == $limit)) $stop = true;
		}
		// return to main admin page when totally completed
	  	if (count($workarray) == 0) {
	  	  	pnSessionDelVar('up_workarray');
		    LogUtil::registerStatus(_USERPICTURESFUNCTIONDONE);
		    return pnRedirect(pnModURL('UserPictures','admin','main'));
		}
		
		// write log message
		LogUtil::registerStatus(_USERPICTURESAVATARSETFOR.': '.$c);
		
		// update session var
		pnSessionDelVar('up_workarray');
		pnSessionSetVar('up_workarray', $workarray);
		// we are ready for the next step now...
	}
	$render->assign('startwith', $startwith);
	$render->assign('authid', SecurityUtil::generateAuthKey());
    
    // Return output
    return $render->fetch('userpictures_admin_templatetoavatar.htm');
}

/**
 * pictures category management
 * 
 * @return       output
 */
function UserPictures_admin_categories()
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output object
	$render = FormUtil::newpnForm('UserPictures');
    return $render->pnFormExecute('userpictures_admin_categories.htm', new userpictures_admin_categorieshandler());
}

/**
 * delete picture
 *
 * This is the delete-a-picture-function for the administrator
 *
 * @return       output
 */
function UserPictures_admin_deletePicture()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    $action 	= FormUtil::getPassedValue('action','');
    $picture_id	= FormUtil::getPassedValue('picture_id');
    if (isset($action) && (strlen($action) > 0)) {
		// auth-key check here!
		if (!pnSecConfirmAuthKey()) LogUtil::registerPermissionError();
		else if ($action == 'delete') {
		    if (pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$picture_id))) LogUtil::registerStatus(_USERPICTURESDELETED);
		    else LogUtil::registerError(_USERPICTURESDELETEERROR);
		}
    }

    // Create output 
    $pnRender 	= pnRender::getInstance('UserPictures');

	// Assign data
    $pnRender->assign('uid',		$uid);
    $pnRender->assign('authid',		SecurityUtil::generateAuthKey());
    $pnRender->assign('picture_id',	$picture_id);

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_deletepicture.htm');
}

/**
 * find orphans
 *
 * This function searches for orphans in the filesystem, the data-
 * base and tries to detect inconsistent in the database
 *
 * @return       output
 */
function UserPictures_admin_findOrphans()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

	$pictures 	= pnModAPIFunc('UserPictures','user','get');
    $action 	= FormUtil::getPassedValue('action');
    if (isset($action) && ($action != '')) {
		// auth-key check here!
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else if ($action == 'deletefiles') {
		    pnModAPIFunc('UserPictures','admin','getOrphanFiles',array(
				'delete'	=> 1,
				'pictures'	=> $pictures
					));
		    LogUtil::registerStatus(_USERPICTURESFILESDELETED);
		}
		else if ($action == 'deletedbfiles') {
		    pnModAPIFunc('UserPictures','admin','getOrphanDBFiles',array(
				'delete'	=> 1,
				'pictures'	=> $pictures
					));
		    LogUtil::registerStatus(_USERPICTURESDBFILESDELETED);
		}
		else if ($action == 'deleteorphanpics') {
		    pnModAPIFunc('UserPictures','admin','getOrphanPictures',array(
				'delete'	=>1,
				'pictures'	=> $pictures
					));
		    LogUtil::registerStatus(_USERPICTURESORPHANPICSDELETED);
		}
    }

    // Create output 
    $pnRender 	= pnRender::getInstance('UserPictures');

	// get data

    $pics		= pnModAPIFunc('UserPictures','admin','getOrphanPictures', array('pictures' => $pictures));
    $files		= pnModAPIFunc('UserPictures','admin','getOrphanFiles', array('pictures' => $pictures));
    $amount 	= pnModAPIFunc('UserPictures','admin','getNumberOfFiles');
	$dbfiles 	= pnModAPIFunc('UserPictures','admin','getOrphanDBFiles', array('pictures' => $pictures));

	// assign data and authkey
    $pnRender->assign('pics',		$pics);
    $pnRender->assign('files',		$files);
    $pnRender->assign('amount',		$amount);
    $pnRender->assign('dbfiles',	$dbfiles);
	$pnRender->assign('authid',		SecurityUtil::generateAuthkey());

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_findorphans.htm');
}

/**
 * templates
 *
 * This is the administration function for template amnagement
 *
 * @return       output
 */
function UserPictures_admin_templates()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output
    $pnRender 	= pnRender::getInstance('UserPictures');

	// get passed parameters
    $action		= FormUtil::getPassedValue('action','');
    
    // check for action
    if ($action != '') {
	// implement auth-key-check here later...

	if ($action=='delete') {
	    // delete all uploaded pictures associated with the template
	    $template_id 	= FormUtil::getPassedValue('template_id');
	    $pictures		= pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template_id));
	    foreach ($pictures as $picture) {
			if (!pnModAPIFunc('UserPictures','user','deletePicture',array('uid'=>$picture[uid],'template_id'=>$picture[template_id]))) {
			    LogUtil::registerStatus(_DELETEERROR); 
			    return pnRedirect(pnModURL('UserPictures','admin','templates'));
			}
	    }
	    if (!pnModAPIFunc('UserPictures','admin','deleteTemplate',array('template_id'=>$template_id))) LogUtil::registerError(_DELETEERROR);
	    else LogUtil::registerStatus(_USERPICTURESDELETED);
	}	
	else if ($action=='edit') {
	    // get stored data and assign this data
	    $template=pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>pnVarCleanFromInput('template_id')));
	    // assign
	    $pnRender->assign('id',				$template[id]);
	    $pnRender->assign('title',			$template[title]);
	    $pnRender->assign('max_width',		$template[max_width]);
	    $pnRender->assign('max_height',		$template[max_height]);
	    $pnRender->assign('defaultimage',	$template[defaultimage]);
	}
	else if ($action=='send') {
	    list (	$id,
			$title,
			$max_width,
			$max_height,
			$defaultimage) = pnVarCleanFromInput(  'id',
								  'title',
								  'max_width',
								  'max_height',
								  'defaultimage'
								    );
	    $pnRender->assign('id',				$id);
	    $pnRender->assign('title',			$title);
	    $pnRender->assign('max_width',		$max_width);
	    $pnRender->assign('max_height',		$max_height);
	    $pnRender->assign('defaultimage',	$defaultimage);

	    // verify the input
	    $flag=false;
	    if (!(strlen($title)>0)) {
			$flag=true;
			LogUtil::registerError(_USERPICTURESNOTITLE);
	    }
	    if (!($max_width>0)) {
			$flag=true;
			LogUtil::registerError(_USERPICTURESNOWITDH);
	    }
	    if (!($max_height>0)) {
			$flag=true;
			LogUtil::registerError(_USERPICTURESNOHEIGHT);
	    }
	    if (!$flag) {
			if (pnModAPIFunc('UserPictures','admin','storeTemplate',array(	'id'			=> $id,
																			'title'			=> $title,
																			'max_width'		=> $max_width,
																			'max_height'	=> $max_height,
																			'defaultimage'	=> $defaultimage))) {
			    LogUtil::registerStatus(_USERPICTURESSETTINGSSTORED);
			    return pnRedirect(pnModURL('UserPictures','admin','templates'));
			}
			else LogUtil::registerError(_USERPICTURESERRORSAVING);
		    }
		}
    }
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_templates.htm');
}
?>