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

	// Get parameter
    $uid = FormUtil::getPassedValue('uid');
    if (!isset($uid) || (!($uid>0))) {
		logUtil::registerError(_USERPICTURESNOUSERSPECIFIED);
		return pnRedirect(pnModURL('UserPictures','user','main'));
    }

	$startwith = (int) FormUtil::getPassedValue('upstartwith');
	print pnModAPIFunc('UserPictures','user','latest',array(
		'template_id'	=> 0,
		'numcols'		=> 4,
		'numrows'		=> 2,
		'uid'			=> $uid,
		'startwith'		=> $startwith
		));

	return;
}

