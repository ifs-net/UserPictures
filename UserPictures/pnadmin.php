<?php
/**
 * the main administration function
 *
 * @return       output       The main module admin page.
 */
function UserPictures_admin_main()
{
    // Security check
    if (!pnSecAuthAction(0, 'UserPictures::', '::', ACCESS_ADMIN)) {
        return pnVarPrepHTMLDisplay(_MODULENOAUTH);
    }

    $action=pnVarCleanFromInput('action');
    if (isset($action)) {
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
    	else if ($action=='deletethumbnails') LogUtil::registerStatus(_USERPICTURESDELETEDTHUMBNAILS.": ".pnModAPIFunc('UserPictures','admin','deletethumbnails'));
    	else if ($action=='send') {
		    $thumbnailsize=FormUtil::getPassedValue('thumbnailsize');
		    $avatarsize=FormUtil::getPassedValue('avatarsize');
		    $datadir=FormUtil::getPassedValue('datadir');
		    if (!isset($datadir) || (!(strlen($datadir)>0))) $datadir="modules/UserPictures/data/";
		    $activated=FormUtil::getPassedValue('activated',0);
		    if ($activated!=1) $activated=0;
		    $hint=FormUtil::getPassedValue('hint',0);
		    if ($hint!=1) $hint=0;
		    $avatarmanagement=FormUtil::getPassedValue('avatarmanagement',0);
		    if ($avatarmanagement!=1) $avatarmanagement=0;
		    $maxfilesize=FormUtil::getPassedValue('maxfilesize',1500);
		    if (!($maxfilesize>0)) $maxfilesize=1500;
		    $ownuploads=FormUtil::getPassedValue('ownuploads',0);
		    if (!($ownuploads>0)) $ownuploads=0;
		    $convert=FormUtil::getPassedValue('convert');
		    if (!(strlen($convert)>0)) $convert='/usr/bin/convert';
		    $disabledtext=FormUtil::getPassedValue('disabledtext');
		    $verifytext=FormUtil::getPassedValue('verifytext');
		    $tc = FormUtil::getPassedValue('thumbnailcreation');
	
		    $maxwidth=FormUtil::getPassedValue('maxwidth');
		    if (!($maxwidth>0)) $maxwidth=800;
		    $maxheight=FormUtil::getPassedValue('maxheight');
		    if (!($maxheight>0)) $maxheight=600;
	
		    // set new value for module var
		    pnModSetVar('UserPictures','thumbnailsize',$thumbnailsize);
		    pnModSetVar('UserPictures','avatarsize',$avatarsize);
		    pnModSetVar('UserPictures','activated',$activated);
		    pnModSetVar('UserPictures','avatarmanagement',$avatarmanagement);
		    pnModSetVar('UserPictures','verifytext',$verifytext);
		    pnModSetVar('UserPictures','disabledtext',$disabledtext);
		    pnModSetVar('UserPictures','maxfilesize',$maxfilesize);
		    pnModSetVar('UserPictures','convert',$convert);
		    pnModSetVar('UserPictures','ownuploads',$ownuploads);
		    pnModSetVar('UserPictures','maxwidth',$maxwidth);
		    pnModSetVar('UserPictures','maxheight',$maxheight);
		    pnModSetVar('UserPictures','datadir',$datadir);
		    pnModSetVar('UserPictures','thumbnailcreation',$tc);
		    pnModSetVar('UserPictures','hint',$hint);
		}
    }

    // Create output 
    $pnRender = pnRender::getInstance('UserPictures');

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_main.htm');
}

/**
 * delete picture
 *
 * @return       output       The main module admin page.
 */
function UserPictures_admin_deletePicture()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    $action=FormUtil::getPassedValue('action','');
    if (isset($action)) {
		// auth-key check here!
		if (!pnSecConfirmAuthKey()) LogUtil::registerPermissionError();
		else if ($action=='delete') {
		    $picture_id=FormUtil::getPassedValue('picture_id');
		    if (pnModAPIFunc('UserPictures','admin','deletePicture',array('picture_id'=>$picture_id))) LogUtil::registerStatus(_USERPICTURESDELETED);
		    else LogUtil::registerError(_USERPICTURESDELETEERROR);
		}
    }

    // Create output 
    $pnRender = pnRender::getInstance('UserPictures');

    $ugall=FormUtil::getPassedValue('ugall');
    $uid=FormUtil::getPassedValue('uid');

    $pnRender->assign('uid',$uid);

    $pnRender->assign('picture_id',FormUtil::getPassedValue('picture_id'));

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_deletepicture.htm');
}

/**
 * find orphans
 *
 * @return       output       The main module admin page.
 */
function UserPictures_admin_findOrphans()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    $action=FormUtil::getPassedValue('action','');
    if ($action != '') {
		// auth-key check here!
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else if ($action=='deletefiles') {
		    pnModAPIFunc('UserPictures','admin','getOrphanFiles',array('delete'=>1));
		    LogUtil::registerStatus(_USERPICTURESFILESDELETED);
		}
		else if ($action=='deletedbfiles') {
		    pnModAPIFunc('UserPictures','admin','getOrphanDBFiles',array('delete'=>1));
		    LogUtil::registerStatus(_USERPICTURESDBFILESDELETED);
		}
		else if ($action=='deleteorphanpics') {
		    pnModAPIFunc('UserPictures','admin','getOrphanPictures',array('delete'=>1));
		    LogUtil::registerStatus(_USERPICTURESORPHANPICSDELETED);
		}
    }

    // Create output 
    $pnRender = pnRender::getInstance('UserPictures');

    $pics=pnModAPIFunc('UserPictures','admin','getOrphanPictures');
    $pnRender->assign('pics',$pics);

    $files=pnModAPIFunc('UserPictures','admin','getOrphanFiles');
    $pnRender->assign('files',$files);
    $pnRender->assign('amount',pnModAPIFunc('UserPictures','admin','getNumberOfFiles'));

    $dbfiles=pnModAPIFunc('UserPictures','admin','getOrphanDBFiles');
    $pnRender->assign('dbfiles',$dbfiles);

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_findorphans.htm');
}

/**
 * the main administration function for browsing stored images
 *
 * @return       output       The main module admin page.
 */
function UserPictures_admin_browser()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender = pnRender::getInstance('UserPictures');

    // get templates
    $template_id=FormUtil::getPassedValue('template_id');
    if (isset($template_id) && ($template_id>=0)) {
		$template=pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$template_id));
		$pnRender->assign('template',$template);
		$pictures=pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template_id));
		$pnRender->assign('picturesCounter',count($pictures));
		$startnum=FormUtil::getPassedValue('startnum',1);
		if (!isset($startnum) || (!($startnum>=0))) $startnum=1;
		$startnum--;
		for ($i=$startnum;$i<=($startnum+20);$i++) if ($pictures[$i][filename]!='') $pics[]=$pictures[$i];
		$pnRender->assign('startnum',$startnum);
		$pnRender->assign('pictures',$pics);
	    }

    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_browser.htm');
}

/**
 * the main administration function for browsing stored images that have to be activated
 *
 * @return       output       The main module admin page.
 */
function UserPictures_admin_toactivate()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $pnRender = pnRender::getInstance('UserPictures');

    $action=FormUtil::getPassedValue('action','');
    if ($action=='activate') {
		// auth key check here
		if (!SecurityUtil::confirmAuthKey()) LogUtil::registerAuthIDError();
		else {
		    $template_id =	(int)FormUtil::getPassedValue('template_id');
		    $uid =			(int)FormUtil::getPassedValue('uid');
		    if (pnModAPIFunc('UserPictures','admin','activatePicture',array('template_id'=>$template_id,'uid'=>$uid))) LogUtil::registerStatus(_USERPICTURESACTIVATED);
		    else LogUtil::registerError(_USERPICTUREACTIVATEFAILED);
		}
    }


    // get templates
    $templates=pnModAPIFunc('UserPictures','admin','getTemplates');
    $pictures=array();
    $i=0;
    foreach ($templates as $template) {
		if ($template[to_verify]=='1')	{
		    $p=pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template[id],'verified'=>0));
		    $pictures=array_merge($p,$pictures);
		    $i++;
		}
		if ($i==20) break;
    }
    $pnRender->assign('pictures',$pictures);
    // Return the output that has been generated by this function
    return $pnRender->fetch('userpictures_admin_toactivate.htm');
}

/**
 * the main administration function for template amnagement
 *
 * @return       output
 */
function UserPictures_admin_templates()
{
    // Security check
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();

    // Create output
    $pnRender = pnRender::getInstance('UserPictures');

    $action=FormUtil::getPassedValue('action','');
    if ($action != '') {
	// implement auth-key-check here later...

	if ($action=='delete') {
	    // delete all uploaded pictures associated with the template
	    list($template_id)=pnVarCleanFromInput('template_id');
	    $pictures=pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template_id));
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
	    $pnRender->assign('to_verify',		$template[to_verify]);
	}
	else if ($action=='send') {
	    list (	$id,
			$title,
			$max_width,
			$max_height,
			$defaultimage,
			$to_verify	) = pnVarCleanFromInput(  'id',
								  'title',
								  'max_width',
								  'max_height',
								  'defaultimage',
								  'to_verify'
								    );
	    $pnRender->assign('id',				$id);
	    $pnRender->assign('title',			$title);
	    $pnRender->assign('max_width',		$max_width);
	    $pnRender->assign('max_height',		$max_height);
	    $pnRender->assign('defaultimage',	$defaultimage);
	    $pnRender->assign('to_verify',		$to_verify);

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
																			'defaultimage'	=> $defaultimage,
																			'to_verify'		=> $to_verify))) {
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