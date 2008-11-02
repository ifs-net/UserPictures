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
 * get global category
 *
 * @param	$args['id']		int		optional
 * @return	array
 */
function UserPictures_adminapi_getGlobalCategory($args)
{
  	$id = (int)$args['id'];
  	if ($id > 0) return DBUtil::selectObjectByID('userpictures_globalcategories',$id);
	else return DBUtil::selectObjectArray('userpictures_globalcategories','','date');
}

/**
 * delete global category
 *
 * @param	$args['id']		int		global category id
 * @return	boolean
 */
function UserPictures_adminapi_delGlobalCategory($args)
{
	// get oarameter
	$id = (int)$args['id'];
	// delete all existing associations
	$objArray = pnModAPIFunc('UserPictures','user','get',array('globalcat_id' => $id));
	foreach ($objArray as $obj) {
	  	$obj['global_category'] = 0;
	  	DBUtil::updateObject($obj,'userpictures');
	}
	// get category and delete it
	$obj = UserPictures_adminapi_getGlobalCategory(array('id' => $id));
	return DBUtil::deleteObject($obj,'userpictures_globalcategories');
}

/**
 * find orphan pictures
 *
 * This function retrieves all pictures whoose owner 
 * does not exist any more
 *
 * @param	$args[delete]		int
 * @param	$args['pictures']	array
 * @return	array
 */
function UserPictures_adminapi_getOrphanPictures($args)
{
	$pictures = $args['pictures'];
  	$pics 	= array();
	foreach ($pictures as $picture) {
	  	$uid 	= $picture['uid'];
	  	$uname 	= pnUserGetVar('uname',$uid);
	  	if (!(strlen($uname) > 0)) {
	  	  	if ($delete > 0) {
			    pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id' => $pic['picture_id']));
			}
			else {
		  	  	$pic = array();
				$pic['picture_id']	= $picture['id'];
				$pic['uid']			= $picture['uid'];
				$pic['template_id']	= $picture['template_id'];
				$pics[]=$pic;
			}
		}
	}
	return $pics;
}

/**
 * get orphan db entries
 *
 * This function retrieves all pictures that are listet in the database but the
 * linked file is no more existent in the filesystem / data directory
 *
 * @param	$args[delete]		int
 * @param	$args['pictures']	array
 * @return	array
 */
function UserPictures_adminapi_getOrphanDBFiles($args)
{
  	// get path to files
    $pictures	= $args['pictures'];
    $delete		= (int)$args['delete'];
    $files 		= array();
    foreach($pictures as $picture) {
	  	if (!file_exists($picture['filename_absolute'])) {
	  	  	if ($delete > 0) {
				pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id' => $picture['id']));
			}
			else {
		  	  	$file 			= array();
				$file[filename]	= $picture[filename];
				$file[uid]		= $picture[uid];
				$files[]		= $file;
			}
		}
	}
	return $files;
}

/**
 * get Orphan files
 *
 * This function retrieves all files that have no entry in the database
 *
 * @param	$args['delete']		int
 * @param	$args['pictures']	array
 * @return	array
 */
function UserPictures_adminapi_getOrphanFiles($args)
{
    $prefix 	= pnModGetVar('UserPictures','datadir');
    $path 		= $prefix;
    $verz 		= opendir($path);
    $delete		= (int)$args['delete'];
    $pictures	= $args['pictures'];

	// reorder picture array
	$dummy = array();
	foreach ($pictures as $picture) {
		$dummy[$picture['filename']]=$picture;
	}
	$pictures 	= $dummy;
	$items 		= array();
	// now scan the datadir
    while ($file = readdir($verz)) {
      	// we have some exclusions
		if ((	filetype($path.$file) != "dir") && 
				($file != "..")  && 
				($file != ".") && 
				($file != "index.html") && 
				($file != "index.htm") && 
				($file != ".htaccess")	) {
			$id = $pictures[str_replace('.thumb.jpg','',$file)]['id'];
			if (!($id > 0)) {
				// delete file if needed
				if ((int)$args['delete'] > 0) unlink($path.$file);
			  	$item['filename'] = $file;
			  	$items[] = $item;
			}
		}
    }
    return $items;
}

/**
 * delete all thumbnails
 *
 * @return 	bool
 */
function UserPictures_adminapi_deletethumbnails()
{
    $prefix = pnModGetVar('UserPictures','datadir');
    $path	= $prefix;
    $verz	= opendir($path);
    $c = 0;
    while ($file = readdir($verz)) {
	if (	(filetype($path.$file)!="dir") && 
			($file != "..")  && 
			($file != ".") && 
			($file != "index.html") && 
			($file != "index.htm") && 
			($file != ".htaccess")	){
	    if (eregi('.thumb.jpg',$file)) {
			if (unlink($prefix."/".$file)) $c++;
		    }
		}
    }
    return $c;
}

/**
 * get number of files that are in the filesystem
 *
 * @return 	int
 */
function UserPictures_adminapi_getNumberOfFiles()
{
    $prefix = pnModGetVar('UserPictures','datadir');
    $path	= $prefix;
    $verz	= opendir($path);
    while ($file = readdir($verz)) $i++;
    return $i;
}


/**
 * create a new UserPictures item
 * 
 * @return   int              UserPictures item ID on success, false on failure
 */
function UserPictures_adminapi_storeTemplate($args)
{
	// get data
    list(	$id,
		$title,
		$max_width,
		$max_height,
		$defaultimage	) = pnVarPrepForStore(	$args[id],
							$args[title],
							$args[max_width],
							$args[max_height],
							$args[defaultimage]	);
	
    // Store to DB
  	$obj = array (
  		'title'			=> $title, 
  		'max_width'		=> (int)$max_width,
  		'max_height'	=> (int)$max_height,
  		'defaultimage'	=> $defaultimage
	  );
	if ($id > 0) {
		$obj['id'] = $id;
		return DBUtil::updateObject($obj,'userpictures_templates');
	}
	else return DBUtil::insertObject($obj,'userpictures_templates');
}

/**
 * get all templates
 *
 * @param 	$args[template_id]	int	optional
 * @return	array
 */
function UserPictures_adminapi_getTemplates($args) 
{
    // Get DB Setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // Get Tables
    $userpictures_templatestable  = &$pntable['userpictures_templates'];
    $userpictures_templatescolumn = &$pntable['userpictures_templates_column'];
    
    // just get one?
    $id=$args[template_id];
    $oneonly=false;
    if (isset($id) && ($id>0)) {
		$where=" WHERE $userpictures_templatescolumn[id] = '".(int)$id ."'";
		$oneonly=true;
    }
    else if (isset($id) && ($id==0)) {
		// we don't have a real template... lets get the important things from the modul-vars
		$item[id]=0;
		$item[title]=_USERPICTURESOWNGALLERY;
		$item[max_width]=pnModGetVar('UserPictures','maxwidth');
		$item[max_height]=pnModGetVar('UserPictures','maxheight');
		$item[defaultimage]='';
		return $item;
    }
    
    $sql = "SELECT 	$userpictures_templatescolumn[id],
			$userpictures_templatescolumn[title],
			$userpictures_templatescolumn[max_width],
			$userpictures_templatescolumn[max_height],
			$userpictures_templatescolumn[defaultimage]
	    FROM $userpictures_templatestable	
	    ".$where;
    $result = $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
	pnSessionSetVar('errormsg', _SELECTFAILED);
        return false;
    }
    $items=array();
    for (; !$result->EOF; $result->MoveNext()) {
	unset($item);
        list(   $item[id],
		$item[title],
		$item[max_width],
		$item[max_height],
		$item[defaultimage]) = $result->fields;
	if ($oneonly) return $item;
	$items[]=$item;
    }
    return $items;
}

/**
 * delete an template
 * 
 * @param    $args[template_id]   int
 * @return   bool           true on success, false on failure
 */
function UserPictures_adminapi_deleteTemplate($args)
{
    // Get datbase setup 
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // get Tables
    $userpictures_templatestable  = &$pntable['userpictures_templates'];
    $userpictures_templatescolumn = &$pntable['userpictures_templates_column'];

    // Delete SQL_statement
    $sql = "DELETE FROM $userpictures_templatestable
            WHERE $userpictures_templatescolumn[id] = '" . (int)pnVarPrepForStore($args[template_id]) ."'
	    LIMIT 1";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * template to avatar automatically function
 *
 * This function makes a given template-picture to the avatar picture for all users
 *
 * @param	$args['template']	array
 * @return 	void
 */
function UserPictures_adminapi_templateToAvatar($args)
{
    // Security check 
    if (!SecurityUtil::checkPermission('UserPictures::', '::', ACCESS_ADMIN)) return false;
  	$template = $args['template'];
	// Check auth key
	if (!SecurityUtil::confirmAuthKey()) {
		LogUtil::registerAuthIDError();
	  	return pnRedirect(pnModURL('UserPictures','admin','templatetoavatar'));
	}
	$workarray 	= pnSessionGetVar('up_workarray');	
	if (!(isset($workarray) && is_array($workarray))) {
	  	// There is no work array set yet - we'll construct this once for its usage
		// get all pictures of the template first. 
		$pictures = pnModAPIFunc('UserPictures','user','get',array('template_id' => $template['id']));
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
//	$limit = 250;
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

	// write log message
	LogUtil::registerStatus(_USERPICTURESAVATARSETFOR.': '.$c);
	// return to main admin page when totally completed
	if (count($workarray) == 0) {
	  	pnSessionDelVar('up_workarray');
		// write log message
	    LogUtil::registerStatus(_USERPICTURESFUNCTIONDONE);
	    return pnRedirect(pnModURL('UserPictures','admin','main'));
	}
		
	// update session var
	pnSessionDelVar('up_workarray');
	pnSessionSetVar('up_workarray', $workarray);
	// we are ready for the next step now...
	return;  
}
?>