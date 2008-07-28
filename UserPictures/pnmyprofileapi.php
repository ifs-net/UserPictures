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
 * on Load function
 *
 * @return void
 */
function UserPictures_myprofileapi_onLoad() {
	// Add some page vars
    PageUtil::addVar('stylesheet','modules/UserPictures/pnincludes/lightbox/css/lightbox.css');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/prototype.js');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/scriptaculous.js?load=effects,builder');
    PageUtil::addVar('javascript','modules/UserPictures/pnincludes/lightbox/js/lightbox.js');
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
    // check if list should be shown
    $uid=pnVarCleanFromInput('uid');
    if (!isset($uid) || (!($uid>0))) {
	pnSessionSetVar('erormsg',_USERPICTURESNOUSERSPECIFIED);
	return pnRedirect(pnModURL('UserPictures','user','main'));
    }

    // Security check removed - we will use our own security check ;-)
    $settings=pnModAPIFunc('UserPictures','user','getSettings',array('uid'=>$uid));
    if (($settings['picspublic']!=1) && (!pnUserLoggedIn())) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }


    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender =& new pnRender('UserPictures');

    // pager
    $startnumthumb = pnVarCleanFromInput('startnumthumb');
    if (!isset($startnumthumb) || (!($startnumthumb>0))) $startnumthumb = 1;
    $pnRender->assign('startnumthumb',$startnumthumb);

    $pnRender->assign('uid',$uid);
    $pnRender->assign('viewer_uid',pnUsergetVar('uid'));

    // there might be a category filter active
    $cat_id=(int)pnVarCleanFromInput('cat_id');
    $pnRender->assign('cat_id',$cat_id);
    // let's get the pictures
    $pictures=pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>0,'uid'=>$uid,'cat_id'=>$cat_id));
    $picturesselected=pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>0,'uid'=>$uid,'cat_id'=>$cat_id,'startnumthumb'=>$startnumthumb));
    $pnRender->assign('picturesselected',$picturesselected);
    $pnRender->assign('pictures',$pictures);
    $pnRender->assign('picturecounter',count($pictures));
    $startnum=pnVarCleanFromInput('startnum');
    if (!isset($startnum) or (!($startnum>=0))) $startnum=1;
    $pnRender->assign('startnum',$startnum);

	// for lightbox integration
    $diffPics = pnModAPIFunc('UserPictures','user','picturesDiff',array(	'allPics'	=> $pictures,
																			'shownPics'	=> $picturesselected));
	$pnRender->assign('diffpics',			$diffPics);

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_myprofile_tab.htm');
}

