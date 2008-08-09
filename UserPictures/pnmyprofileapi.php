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
 * on Load function
 *
 * @return void
 */
function UserPictures_myprofileapi_onLoad() {
  	PageUtil::AddVar('javascript','javascript/ajax/prototype.js');
  	PageUtil::AddVar('javascript','javascript/ajax/lightbox.js');
  	PageUtil::AddVar('stylesheet','javascript/ajax/lightbox/lightbox.css');
  	PageUtil::AddVar('javascript','javascript/overlib/overlib.js');
  	PageUtil::AddVar('javascript','javascript/ajax/scriptaculous.js');
}

/**
 * This function returns 1 if Ajax should not be used loading the plugin
 *
 * @return string
 */

function UserPictures_myprofileapi_noAjax($args)
{
  	return true;
}

/**
 * This function returns the name of the tab
 *
 * @return string
 */
function UserPictures_myprofileapi_getTitle($args)
{
    pnModLangLoad('UserPictures','myprofile');
    return _USERPICTURESTABTITLE;
}

/**
 * This function returns additional options that should be added to the plugin url
 *
 * @return string
 */
function UserPictures_myprofileapi_getURLAddOn($args)
{
    return '';
}

/**
 * This function shows the content of the main MyProfile tab
 *
 * @return output
 */
function UserPictures_myprofileapi_tab($args)
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_OVERVIEW)) return LogUtil::registerPermissionError();

    // check if list should be shown
    $uid = FormUtil::getPassedValue('uid');
    if (!isset($uid) || (!($uid>0))) {
		logUtil::registerError(_USERPICTURESNOUSERSPECIFIED);
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }

    // Security check removed - we will use our own security check ;-)
    $settings=pnModAPIFunc('UserPictures','user','getSettings',array('uid'=>$uid));
    if (($settings['picspublic']!=1) && (!pnUserLoggedIn())) {
        return LogUtil::registerPermissionError();
    }

	$startwith = (int) FormUtil::getPassedValue('upstartwith');
	print pnModAPIFunc('UserPictures','user','latest',array(
		'template_id'	=> 0,
		'numcols'		=> 4,
		'numrows'		=> 3,
		'nopager'		=> 0,
		'startwith'		=> $startwith
		));

	return;
    // get pictures
    $pictures = pnModAPIFunc('UserPictures','user','get',array (
    		'uid'			=> $uid,
    		'template_id'	=> 0,
    		'startwith'		=> $startwith,
    		'showmax'		=> 20,
    		'expand'		=> true
		));
	// get number of pictures for counter
    $pictures_count = pnModAPIFunc('UserPictures','user','get',array (
    		'uid'			=> $uid,
    		'template_id'	=> 0,
    		'countonly'		=> true
		));

	// create output
	$render = pnRender::getInstance('UserPictures');

	$render->assign('pictures_count',		$pictures_count);
	$render->assign('pictures',				$pictures);

	$render->display('userpictures_myprofile_tab.htm');
	return;
}

