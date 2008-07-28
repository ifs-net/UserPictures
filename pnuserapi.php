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
 * Show Picture function
 *
 * this function should be used from your templates to include pnUserPictures!
 *
 * @param $args['uid']		int
 * @param $args['template_id']	int
 * @return	array
 */
function UserPictures_userapi_showPicture($args)
{
    $arr=UserPictures_userapi_getPicture(array('uid'=>$args['uid'],'template_id'=>$args['template_id'],'code'=>1));
    return $arr[0];
}

/**
 * get not displayed pictures of a page for slideshow
 *
 * This function is essential if the embedded lightbox is used to
 * get all and not only the pictures of one page into the slideshow
 *
 * @param	$args['allPics']	array
 * @param	$args['shownPics']	array
 * @return	array
 */
function UserPictures_userapi_picturesDiff($args)
{
  	$a = $args['allPics'];
  	$s = $args['shownPics'];
  	// reorder allPics array with id as key value;
  	foreach ($a as $x) $allPics[$x['id']] = $x;
  	// delete key and value if double
  	foreach ($s as $x) if ($allPics[$x['id']]['id'] > 0) unset($allPics[$x['id']]);
  	return $allPics;
}

/**
 * get order array for noscript ajax fallback ordering the item list
 *
 * @param	$fields		array
 * @return	array
 */
function UserPictures_userapi_addOrderLinkToPictures($args)
{
  	// include external functions
	Loader::requireOnce('modules/UserPictures/pnincludes/functions.userpictures.php');
	
  	$fields = $args['pictures'];
  	if (!isset($fields)) return false;
  	
	// we'll now create an array which contains the array for 
	// moving an element up and down for every element of the list
  	foreach ($fields as $field) $workArray[] = $field['id'];
  	$i=0;
  	foreach ($workArray as $w) {
  	  	// we'll store the array with the id of the entry as key
  	  	$copy = $workArray;
  	  	if ($i!=0) $res[$workArray[$i]]['up'] = switchArrayElements($copy,($i-1),$i);
  	  	if ($i!=count($fields)-1) $res[$workArray[$i]]['down'] = switchArrayElements($copy,$i,($i+1));
	    $i++;
	}
	foreach ($fields as $field) {
	  	$up = $res[$field['id']]['up'];
		if (count($up)>0)$field['orderlink']['up'] = htmlentities(serialize($up));
		$down = $res[$field['id']]['down'];
		if (count($down)>0)$field['orderlink']['down'] = htmlentities(serialize($down));
		$fieldRes[]=$field;
	}
	return $fieldRes;
}

/**
 * store the new order of the fields
 *
 * @param	$args['list']	array
 * @return 	boolan
 */
function UserPictures_userapi_ajaxSaveList($args) 
{
 	$list = $args['list'];
	if (!isset($list)) return false; 
	foreach ($list as $key=>$value) {
	  	// get item with id "value" and set the new position number "sort" (+1)
	  	$field = DBUtil::selectObjectByID('userpictures',$value);
	  	$field['position'] = $key;
	  	DBUtil::updateObject($field,'userpictures');
	}
	return true;
}

/**
 * add an associaiton to a picture
 *
 * @param	$args['picture_id']	int
 * @param	$args['uname']		int
 * @return	bool
 */
function UserPictures_userapi_addPerson($args)
{
    $picture_id =	(int)$args['picture_id'];
    $uname =		$args['uname'];
    $uid =			pnUserGetIDFromName($uname);
    if (!isset($uid) || (!($uid>1))) return false;
    
    // we need to check if we are allowed to link the picture with the given username.
    $uid = 			pnUserGetIDFromName($uname);
    $picture = 		UserPictures_userapi_getPicture(array(	'picture_id'	=> $picture_id,
															'uid'			=> $uid));
    $picture_uid =	$picture[0]['uid'];
    $settings = 	pnModAPIFunc('UserPictures','user','getSettings',array('uid'=>$uid));
    if (($settings['nolinking'] ==1 ) && ($settings['uid'] != $picture_uid)) return false;
    
    // first delete association to avoid doubles
    UserPictures_userapi_delPerson(array('uname'=>$uname,'picture_id'=>$picture_id));
    
    $obj = array (
    		'picture_id'	=> $picture_id,
    		'uid'			=> $uid
		);
    return DBUtil::insertObject($obj,'userpictures_persons');
} 

/**
 * associate a picture with a global category
 *
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_addToGlobalCategory($args)
{
    $picture_id =	(int)$args['picture_id'];
    $cat_id =		(int)$args['cat_id'];
    if (!isset($cat_id) || (!($cat_id>0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
    
    // delete an association if there is an existing (to avoid doubles)
	UserPictures_userapi_delFromGlobalCategory(array('picture_id'=>$picture_id,'cat_id'=>$cat_id));

	// store in DB
	$obj = array (
			'picture_id'	=> $picture_id,
			'cat_id'		=> $cat_id,
			'date'			=> date("Y-m-d",time())
		);
    return DBUtil::insertObject($obj,'userpictures_globalcatassoc');
} 

/**
 * delete an assoziation from a global category
 *
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_delFromGlobalCategory($args)
{
    $picture_id =	(int)$args['picture_id'];
    $cat_id =		(int)$args['cat_id'];
    if (!isset($cat_id) || (!($cat_id>0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
    
	// Delete category assiciation
    $table =& pnDBGetTables();
    $column = &$table['userpictures_globalcatassoc_column'];
	$where = 	$column['cat_id']." = '" . $cat_id . "'
				AND ".$column['picture_id']." = '" . $picture_id . "'";
		return DBUtil::deleteWhere('userpictures_globalcatassoc',$where);
} 

/**
 * associate a picture with a category
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_addToCategory($args)
{
    $uid = 			(int)$args['uid'];
    $picture_id =	(int)$args['picture_id'];
    $cat_id =		(int)$args['cat_id'];
    if (!isset($uid) || (!($uid>0))) return false;
    if (!isset($cat_id) || (!($cat_id>0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
    
    // delete an association if there is an existing (to avoid doubles)
    UserPictures_userapi_delFromCategory(array('uid'=>$uid,'picture_id'=>$picture_id,'cat_id'=>$cat_id));

	// store in DB
	$obj = array (
			'picture_id'	=> $picture_id,
			'cat_id'		=> $cat_id,
			'uid'			=> $uid
		);
    return DBUtil::insertObject($obj,'userpictures_catassoc');
} 

/**
 * delete an assoziation
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_delFromCategory($args)
{
    $uid =			(int)$args['uid'];
    $picture_id =	(int)$args['picture_id'];
    $cat_id =		(int)$args['cat_id'];
    if (!isset($uid) || (!($uid>0))) return false;
    if (!isset($cat_id) || (!($cat_id>0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
    
	// Delete category assiciation
    $pntable =& pnDBGetTables();
    $userpictures_catassoccolumn = &$pntable['userpictures_catassoc_column'];
	$where = 	$userpictures_catassoccolumn['uid']." = '" . $uid . "'
				AND ".$userpictures_catassoccolumn['cat_id']." = '" . $cat_id . "'
				AND ".$userpictures_catassoccolumn['picture_id']." = '" . $picture_id . "'";
	
	return DBUtil::deleteWhere('userpictures_catassoc',$where);
} 

/**
 * add a new category
 *
 * @param	$args['uid']		int
 * @param	$args['title']		string
 * @param	$args['text']		string
 * @return	bool
 */
function UserPictures_userapi_addCategory($args)
{
    $uid =		(int)$args['uid'];
    $title =	$args['title'];
    $text =		$args['text'];
    if (!isset($uid) || (!($uid>0))) return false;
    if (!isset($title) || (!(strlen($title)>0))) return false;

	// store in DB
	$obj = array (
			'uid'		=> $uid,
			'title'		=> $title,
			'text'		=> $text
		);
	return DBUtil::insertObject($obj,'userpictures_categories');
} 

/**
 * edit or delete a category
 *
 * @param	$args['id']		int
 * @param	$args['uid']		int
 * @param	$args['title']		string
 * @param	$args['text']		string
 * @return	bool
 */
function UserPictures_userapi_editCategory($args)
{
    $uid 	= (int)$args['uid'];
    $title 	= $args['title'];
    $text	= $args['text'];
    $id		= (int)$args['id'];
    $delete	= (int)$args['delete'];
    if (!isset($id) 	|| (!($id>0))) 			return false;
    if (!isset($uid)	|| (!($uid>0))) 		return false;
    if (!isset($text) 	|| (!(strlen($uid)>0)))	return false;
    
    // Get datbase setup and pntables array
    $pntable =& pnDBGetTables();
    $userpictures_categoriescolumn = &$pntable['userpictures_categories_column'];

    // SQL statement 
    if (isset($delete) && ($delete == '1')) {
		// we now have to delete all associations between pictures and this category
		$catAssocs = UserPictures_userapi_getCategoryAssociations(array('cat_id'=>$id));
		foreach ($catAssocs as $assoc) {
		    if (!(pnModAPIFunc('UserPictures','user','delFromCategory',array('uid'=>$uid,'picture_id'=>$assoc['picture_id'],'cat_id'=>$id)))) return false;
		}
		$where = $userpictures_categoriescolumn['uid']." = '".$uid."'
			    AND ". $userpictures_categoriescolumn['id']." = '".$id."'";
		return DBUtil::deleteWhere('userpictures_categories',$where);
    }
    else {
	  	// get object
	  	$obj = DBUtil::selectObjectByID('userpictures_categories',$id);
	  	if ($obj['uid'] != $uid) return false;
	  	$obj['text'] 	= $text;
	  	$obj['title'] 	= $title;
	  	return DBUtil::updateObject($obj,'userpictures_categories');
	}
} 

/**
 * get general settings of a user
 *
 * @param	$args['uid']	int
 * @return	array
 */
function UserPictures_userapi_getSettings($args)
{
	return DBUtil::selectObjectByID('userpictures_settings',(int)$args['uid'],'uid');
}

/**
 * store general settings for a user
 *
 * @param	$args['uid']		int
 * @param	$args['nolinking']	int
 * @param	$args['nocomments']	int
 * @param	$args['picspublic']	int
 * @return	bool
 */
function UserPictures_userapi_setSettings($args)
{
    $uid = (int)$args['uid'];
    if (!($uid > 0)) return false;
    // build settings object
    $obj = array (
    		'uid' 			=> $uid,
    		'nolinking' 	=> (int)$args['nolinking'],
    		'nocomments' 	=> (int)$args['nocomments'],
    		'picspublic' 	=> (int)$args['picspublic']
		);
	// get old settings
	$settings = UserPictures_userapi_getSettings(array('uid' => $uid));
	if (is_array($settings)) return DBUtil::updateObject($obj,'userpictures_settings','','uid');
	else return DBUtil::insertObject($obj,'userpictures_settings');
}
 
/**
 * get categories
 *
 * @param	$args['uid']	int
 * @return	array
 */
function UserPictures_userapi_getCategories($args)
{
	// get user id
    $uid=(int)$args['uid'];

	// get table array    
    $pntable =& pnDBGetTables();
    $userpictures_categoriescolumn = &$pntable['userpictures_categories_column'];
    
    // get objects
    $where 		= $userpictures_categoriescolumn['uid']." = ". (int)$uid;
    $orderby 	= $userpictures_categoriescolumn['title'];
    return DBUtil::selectObjectArray('userpictures_categories',$where,$orderby);
}

/**
 * get a single category
 *
 * @param	$args['cat_id']	int
 * @return	array
 */
function UserPictures_userapi_getCategory($args)
{
    return DBUtil::selectObjectByID('userpictures_categories',(int)$args['cat_id']);
}

/**
 * get associations category -> pictures of this category
 *
 * @param	$args['cat_id']	int
 * @return	array
 */
function UserPictures_userapi_getCategoryAssociations($args)
{
    $cat_id=(int)$args['cat_id'];
    // get table array
    $pntable =& pnDBGetTables();
    $userpictures_catassoccolumn = &$pntable['userpictures_catassoc_column'];
    $where = $userpictures_catassoccolumn['cat_id']." = ". $cat_id;
    // return objects
    return DBUtil::selectObjectArray('userpictures_catassoc',$where);
}


/**
 * get associations for picture -> categories
 *
 * @param	$args['picture_id']	int
 * @return	array
 */
function UserPictures_userapi_getCategoriesAssociation($args)
{
    $picture_id = (int)$args['picture_id'];
    // get table array
    $pntable =& pnDBGetTables();
    $userpictures_catassoccolumn = &$pntable['userpictures_catassoc_column'];
    $where = $userpictures_catassoccolumn['picture_id']." = ". $picture_id;
    // return objects
    return DBUtil::selectObjectArray('userpictures_catassoc',$where);
}

/**
 * get global associations for picture -> categories
 *
 * @param	$args['picture_id']	int
 * @return	array
 */
function UserPictures_userapi_getGlobalCategoriesAssociation($args)
{
    $picture_id = (int)$args['picture_id'];
    // get table array
    $table =& pnDBGetTables();
    $column = &$table['userpictures_globalcatassoc_column'];
    $where = $column['picture_id']." = ". $picture_id;
    // return objects
    return DBUtil::selectObjectArray('userpictures_globalcatassoc',$where);
}

/**
 * delete global association for picture -> categories
 *
 * @param	$args['picture_id']	int
 * @param	$args['uid']		int
 * @return	array
 */
function UserPictures_userapi_delGlobalCategoryAssociation($args)
{
    $picture_id = (int)$args['picture_id'];
    if (!($picture_id > 0)) return false;
    $uid = (int)$args['uid'];
    if (!($uid > 1)) return false;
    // get table array
    $table =& pnDBGetTables();
    $column = &$table['userpictures_globalcatassoc_column'];
    $where = $column['picture_id']." = ". $picture_id;
    // check if user is owner of picture!
    $pic = UserPictures_userapi_getPicture(array(	'uid' 			=> $uid,
													'template_id' 	=> 0,
													'picture_id' 	=> $picture_id));
	if ($uid != $pic[0]['uid']) return false;
	else return DBUtil::deleteWhere('userpictures_globalcatassoc',$where);
}

/**
 * delete an associaiton to a picture
 *
 * @param	$args['picture_id']	int
 * @param	$args['uid']		int
 * @return	bool
 */
function UserPictures_userapi_delPerson($args)
{
    $picture_id	= (int)$args['picture_id'];
    $uname 		= $args['uname'];
    $uid 		= pnUserGetIDFromName($uname);
    if (!isset($uid) || (!($uid>0))) return false;
    
    // Get db table array
    $tables =& pnDBGetTables();
    $userpictures_personscolumn = &$tables['userpictures_persons_column'];
	$where = $userpictures_personscolumn['uid']." = '". $uid  ."'";
    if ($picture_id>0) $where.="    AND 	".$userpictures_personscolumn['picture_id']." = '". $picture_id ."'";
    return DBUtil::deleteWhere('userpictures_persons',$where);
} 

/**
 * get persons associated with an image
 *
 * @param	$args['picture_id']	int
 * @return	array
 */
function UserPictures_userapi_getPersons($args)
{
  	// get db table array
    $tables =& pnDBGetTables();
    $userpictures_personscolumn = &$tables['userpictures_persons_column'];
    
    $joinInfo[] = array (
    		'join_table'			=> 'users',	// table to join with
    		'join_field'			=> 'uname',	// field that should be in the result
    		'object_field_name'		=> 'uname',	// how the field should be named in the array
    		'compare_field_table'	=> 'uid',	// connection to join
    		'compare_field_join'	=> 'uid'	// field that should be equal in the join
		);
    $where = $userpictures_personscolumn['picture_id']." = ". (int)$args['picture_id'];
    return DBUtil::selectExpandedObjectArray('userpictures_persons',$joinInfo,$where);
}

/**
 * clean the filename to the sql filename without path
 *
 * @param	$args['filename']	string
 * @param	$args['template_id']	int
 * @param	$args['uid']		int
 * @return	string
 */
function UserPictures_userapi_cleanFilename($args)
{
    $lastfilename=$args['filename'];
    $uid=$args['uid'];
    $template_id=$args['template_id'];
    $dummy = explode("_".$uid.".",$lastfilename);
    $lastfilename_end=$dummy[(count($dummy)-1)];
    $lastfilename_withoutend=$dummy[(count($dummy)-2)];
    // the timestamp:
    $dummy = explode($template_id."-",$lastfilename_withoutend);
    $ts = (int)$dummy[(count($dummy)-1)];
    $lastfilename = $template_id."-".$ts."_".$uid.".".$lastfilename_end;
    return $lastfilename;
}

/**
 * increase the timestamp that is inside the filename
 *
 * @param	$args['filename']	string
 * @param	$args['value']		string
 * @return	string
 */
function UserPictures_userapi_increaseFilename($args)
{
    $prefix = pnModGetVar('UserPictures','datadir');    
    $value=$args['value'];
    $filename=$args['filename'];
    // first part
    $dummy     = explode ("-",$filename);
    $firstpart = $dummy[0];
    // extract timestamp
    $ts_str    = $dummy[1];
    $ts_dummy  = explode ("_",$ts_str);
    $ts        = $ts_dummy[0];
    // now the end of the file and the user id
    $dummy     = explode ("_",$filename);
    $lastpart  =$dummy[1];
    $result    = $firstpart."-".($ts+$value)."_".$lastpart;
    return $result;    
}

/**
 * rotate a picture
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['template_id']	int
 * @return	bool
 */
function UserPictures_userapi_rotatePicture($args)
{
    $prefix = pnModGetVar('UserPictures','datadir');
    $uid=$args['uid'];
    $template_id=$args['template_id'];
    $picture=UserPictures_userapi_getPicture(array('uid'=>$args['uid'],'picture_id'=>$args['picture_id'],'template_id'=>$args['template_id']));
    $p=$picture[0];
    $filename=$p['filename'];
    if (!strlen($filename)>0) return false;
    $e=explode(".",$filename);
    $ext=$e[(count($e)-1)];
    $tmpfile=$filename."_____tempresize.".$ext;
    $angle=$args['angle'];
    if (!($angle>=0) &&  !($angle<360)) return false;
    $convert=pnModGetVar('UserPictures','convert');
    $cmd="$convert -rotate $angle $filename $tmpfile";
    if (!file_exists($filename)) return false;
    else {
	if ($angle>0) {
	    shell_exec($cmd);
	    unlink($filename);
	    if (file_exists($filename.'.thumb.jpg')) unlink($filename.'.thumb.jpg');
	    rename($tmpfile,$filename);
	}
	
	// the filename is [template_id]-[unix-timestamp]_[uid].jpg
	// we now need to increase the timestamp plus one to avoid
	// caching problems.
	
	// the end of the file:
	$filename = UserPictures_userapi_cleanFilename(array('filename'=>$filename,'uid'=>$uid,'template_id'=>$template_id));
	$new_filename = UserPictures_userapi_increaseFilename(array('filename'=>$filename,'value'=>1));

	// Get datbase setup 
	$dbconn =& pnDBGetConn(true);
	$pntable =& pnDBGetTables();

	// get Tables
	$userpicturestable = $pntable['userpictures'];
	$userpicturescolumn = &$pntable['userpictures_column'];

	// SQL statement 
	$sql = "UPDATE $userpicturestable 
	    SET ".$userpicturescolumn['filename']." = '".pnVarPrepForStore($new_filename)."'
	    WHERE ".$userpicturescolumn['filename']." = '". pnVarPrepForStore($filename) ."'
	    LIMIT 1 ";
	$result = $dbconn->Execute($sql);
	// Check for an error with the database code, and if so set an appropriate
	// error message and return
	if ($dbconn->ErrorNo() != 0) return false;

	// set should be closed when it has been finished with
	$result->Close();
	
	// now we need to rename the file - otherwise the image will be broken
	if (!rename($prefix.$filename,$prefix.$new_filename)) return false;
	else return true;
    }
}


/**
 * change the comment of a picture
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['comment']		string
 * @return	bool
 */
function UserPictures_userapi_setComment($args)
{
  	// get object
	$obj = DBUtil::selectObjectByID('userpictures',(int)$args['picture_id']);
	if ($obj['uid'] != (int)$args['uid']) return false;
	$obj['comment'] = $args['comment'];
	// update object
	return DBUtil::updateObject($obj,'userpictures');
}

/**
 * copyPictureAsAvatar
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['template_id']	int
 * @return	bool
 */
function UserPictures_userapi_copyPictureAsAvatar($args)
{

    // get data and verify data
    $uid = $args['uid'];
    $template_id = $args['template_id'];
    $picture_id = $args['picture_id'];
    if (!($uid>0)  || !($template_id>(-1) || !($picture_id>0))) return false;
    
    // We will now resize the image and copy it into the postnuke avatar folder (images/avatar as standard!)
    $picture=UserPictures_userapi_getPicture(array('picture_id'=>$picture_id));
    $picture=$picture[0];
    $filename=$picture['filename'];
    $avatardir=pnModGetVar('UserPictures','avatardir');
    $targetfilename=$avatardir."/pers_".$uid.".jpeg";
    $avatarsize=pnModGetVar('UserPictures','avatarsize');
    
    // if a avatar with this name exists we have to delete it!
    if (file_exists($targetfilename)) unlink($targetfilename);
    
    // create the image
    UserPictures_userapi_resizePicture(array('filename'=>$filename,'size'=>$avatarsize,'targetfilename'=>$targetfilename));
    
    // Set the user's variable
    if (pnUserSetVar('user_avatar','pers_'.$uid.'.jpeg')) return true;
    else return false;
}

/**
 * create a resized picture
 *
 * @param	$args['filename']	string
 * @param	$args['size']		string
 * @param	$args['targetfilename']	string
 */
function UserPictures_userapi_resizePicture($args)
{
    $convert = pnModGetVar('UserPictures','convert');
    $thumbnailcreation = pnModGetVar('UserPictures','thumbnailcreation');
    $size=$args['size'];
    $filename=$args['filename'];
    $targetfilename=$args['targetfilename'];
    if ($args['hint']) $hint=1;
    else $hint=0;
    
    if ($thumbnailcreation == 'gdlib') {
	$s = explode('x',$size);
	$w1 = (int)$s[0];
	$w2 = (int)$s[1];
	if ($w1>$w2) $longside=$w1;
	else $longside=$w2;
	include_once ('modules/UserPictures/pnincludes/function.thumb.php');
	return XXXsmarty_function_thumb(array('file'=>$filename,'hint'=>$hint,'targetfilename'=>$targetfilename,'longside'=>$longside));
    }
    else { // convert	
	$cmd = "$convert $filename -resize $size $targetfilename";
	shell_exec($cmd);
	return true;
    }
}

/**
 * deletePicture
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['template_id']	int
 * @return	bool
 */
function UserPictures_userapi_deletePicture($args)
{

    // get data and verify data
    $uid = $args['uid'];
    $template_id = $args['template_id'];
    $picture_id = $args['picture_id'];
    if (!($uid>0)  || !($template_id>(-1) || !($picture_id>0))) return false;

    // delete all associated persons
    $dummy = UserPictures_userapi_getPersons(array('picture_id'=>$picture_id));
    foreach ($dummy as $assoc) if (!UserPictures_userapi_delPerson(array('picture_id'=>$picture_id,'uname'=>$assoc['uname']))) return false;
    
    // delete all associated categories
    $dummy = UserPictures_userapi_getCategoriesAssociation(array('picture_id'=>$picture_id));
    foreach ($dummy as $assoc) {
	if (!UserPictures_userapi_delFromCategory(array('uid'=>$uid,'cat_id'=>$assoc['cat_id'],'picture_id'=>$picture_id))) return false;
    }

    // get the picture's filename to delete it
    $picArray=UserPictures_userapi_getPicture(array('uid'=>$uid,'template_id'=>$template_id,'picture_id'=>$picture_id));
    $picture=$picArray[0];
    $filename=$picture['filename'];
    
    // now unlink the file...
    $res=unlink($filename);
    if (!$res) return false;

    // Get db table array
    $tables =& pnDBGetTables();
    $userpicturescolumn = &$tables['userpictures_column'];
	$where = 	$userpicturescolumn['uid']." = '". (int)$uid ."'
			    AND   ".$userpicturescolumn['template_id']." = '". (int)$template_id ."'
			    AND   ".$userpicturescolumn['id']." = '". (int)$picture_id ."'";
	if (!DBUtil::deleteWhere('userpictures',$where)) return false;

    // delete the thumbnail too
    $res=unlink($filename.'.thumb.jpg');
    if (!$res) return false;
    return true;
}

/**
 * storePictureDB
 * 
 * @return   bool
 */
function UserPictures_userapi_storePictureDB($args)
{
    // Get datbase setup 
    $pntable =& pnDBGetTables();

    //get args
    $filename	= $args['filename'];
    $comment	= $args['comment'];
    $template_id= $args['template_id'];

    if (!isset($filename) || (!(strlen($filename)>0))) return false;
    if (!isset($template_id)) return false;
    $dummy = explode('/',$filename);
    // we want to store it without that long path....
    $filename=$dummy[(count($dummy)-1)];

	// store in DB
	$obj = array (
			'uid'			=> pnUserGetVar('uid'),
			'template_id'	=> $template_id,
			'comment'		=> $comment,
			'filename'		=> $filename
		);
	DBUtil::insertObject($obj,'userpictures');
    // now we can associate the picture with its owner's user id
    return UserPictures_userapi_addPerson(array(	'uname'			=> pnUserGetVar('uname'),
													'picture_id'	=> $obj['id']));
}

/**
 * get pictures from database
 *
 * @param	$args['template_id']	int
 * @param	$args['verified']	int
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 */
function UserPictures_userapi_getPictures($args)
{

    $prefix=pnModGetVar('UserPictures','datadir');    

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    // Get tables
    $userpicturestable = $pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];

    $userpictures_templatestable = $pntable['userpictures_templates'];
    $userpictures_templatescolumn = &$pntable['userpictures_templates_column'];

    $userpictures_personstable = $pntable['userpictures_persons'];
    $userpictures_personscolumn = &$pntable['userpictures_persons_column'];

    $userpictures_catassoctable = $pntable['userpictures_catassoc'];
    $userpictures_catassoccolumn = &$pntable['userpictures_catassoc_column'];

    // Get Arguments
    $uid =			$args['uid'];
    $cat_id =		$args['cat_id'];
    $template_id =	$args['template_id'];
    $picture_id =	$args['picture_id'];
    $verified =		$args[verified];
    if (!isset($template_id) || !($template_id>=0)) return false;

    if ($verified=='0') 	$and = " AND $userpicturestable.".$userpicturescolumn['verified']." = '0' ";
    if ($uid>0) 			$and.=" AND $userpicturestable.".$userpicturescolumn['uid']." = '". (int)$uid."' ";
    if ($picture_id>0) 		$and.= " AND $userpicturestable.".$userpicturescolumn['picture_id']." = '". (int)pnVarPrepForStore($picture_id) ."'";
    if ($cat_id > 0 ) {
			$cat_assoc_and = " 
				AND $userpicturestable.".$userpicturescolumn['id']." = $userpictures_catassoctable.".$userpictures_catassoccolumn['picture_id']."
				AND $userpictures_catassoctable.".$userpictures_catassoccolumn['cat_id']." = '".pnVarPrepForStore($cat_id)  ."'";
			$cat_assoc_from = ", ".$userpictures_catassoctable;
    }
    $startnum=$args[startnum];
    if (isset($startnum) && ($startnum >= 0)) $limit = "  LIMIT ".($startnum-1)." ,1 ";
    
    $order = "ORDER BY $userpicturestable.".$userpicturescolumn['position'].", $userpicturestable.".$userpicturescolumn['filename']." DESC";

    // do we need the data for the "latest pictures" thumbnail gallery?
    $startnumthumb=$args[startnumthumb];
    if (isset($startnumthumb) && ($startnumthumb >= 0)) {
	if (isset($args['amount']) && ($args['amount']>0)) $amount = $args['amount'];
	else $amount=50;
	if (isset($args['index']) && ($args['index']==1)) {
	    $limit = "LIMIT 0, $amount";
	    $order = "ORDER BY $userpicturestable.".$userpicturescolumn['id']." DESC";	
	}
	else $limit = "  LIMIT ".($startnumthumb-1)." ,25 ";
    }
    
    // we need to check if we only should load pictures associated with a specified user id
    $assoc_uid=(int)$args['assoc_uid'];
    if (isset($assoc_uid) && ($assoc_uid>0)) {
	$assoc_from = ", ".$userpictures_personstable;
	$assoc_and = " 	AND $userpictures_personstable.".$userpictures_personscolumn['picture_id']." = $userpicturestable.".$userpicturescolumn['id']."
			AND $userpictures_personstable.".$userpictures_personscolumn['uid']." = '". $assoc_uid ."'  ";
	// should we hide the user's own pictures to which he mostly is associated?
	$hideown = $args['hideown'];
	if (isset($hideown) && ($hideown==1)) $assoc_and.=" AND $userpictures_personstable." . $userpictures_personscolumn['uid'] . " != ".$userpicturestable.".".$userpicturescolumn['uid'];
    }
    
    // Get picture
    $sql = "SELECT $userpicturestable.".$userpicturescolumn['filename'].",
		   $userpicturestable.".$userpicturescolumn['uid'].",
		   $userpicturestable.".$userpicturescolumn['id'].",
                   $userpicturestable.".$userpicturescolumn['comment'].",
                   $userpicturestable.".$userpicturescolumn['template_id'].",
                   $userpicturestable.".$userpicturescolumn['verified']."
            FROM $userpicturestable
	    $assoc_from
	    $cat_assoc_from
	    WHERE $userpicturestable.".$userpicturescolumn['template_id']." = '". (int)pnVarPrepForStore($template_id) ."'
	    $thumbnailwhere
	    $and
	    $assoc_and
	    $cat_assoc_and
	    $order
	    $limit
	    ";
    $result =& $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
         pnSessionSetVar('errormsg', 'Failed to get items!'.$sql);
         return false;
    }
    $items=array();
    for (; !$result->EOF; $result->MoveNext()) {
        unset($item);
        list(	$item['filename'],
		$item['uid'],
		$item['id'],
		$item['comment'],
		$item['template_id'],
		$item['verified']) = $result->fields;
	$filename = $prefix.$item['filename'];
	$item['filename']=$filename;

	// create thumbnails if they do not exist!
	if (!UserPictures_userapi_createThumbnail(array('filename'=>$filename))) pnSessionSetVar('errormsg',_USERPICTURESTHUMBNAILCREATIONERROR);
	
	$items[]=$item;
	$lastfilename=$filename;
    }

    // Return the item array
    $result->close();
    return $items;
}

/**
 * create a thumbnail file to an existing image file
 * but only if the thumbnail has not been created yet!
 *
 * @param	$args['filename']	string
 * @return	bool
 */
function UserPictures_userapi_createThumbnail($args)
{
	$filename =			$args['filename'];
	$thumbnail = 		$filename.".thumb.jpg";
    $convert =			pnModGetVar('UserPictures','convert');
    $thumbnailsize =	pnModGetVar('UserPictures','thumbnailsize');
	if (!(file_exists($thumbnail))) return UserPictures_userapi_resizePicture(array(	'filename'			=> $filename,
																						'size'				=> $thumbnailsize,
																						'targetfilename'	=> $thumbnail,
																						'hint'				=> pnModGetVar('UserPictures','hint')));
	else return true;
}

/**
 * get a picture from the database
 *
 * @param	$args['uid]		int
 * @param	$args['template_id]	int
 * @param	$args['picture_id]	int
 * @return	array
 */
function UserPictures_userapi_getPicture($args)
{
    // we need this variable for the thumbnail creation
    $convert=pnModGetVar('UserPictures','convert');
    $thumbnailsize=pnModGetVar('UserPictures','thumbnailsize');
    $prefix = pnModGetVar('UserPictures','datadir');

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // Get tables
    $userpicturestable = $pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];

    // Get Arguments and ch[21~eck them
    $uid=(int)$args['uid'];
    $picture_id=$args['picture_id'];
    if (!isset($picture_id)) {
	if (!isset($uid) || !($uid>0)) return false;
	$template_id=$args['template_id'];
	if (!isset($template_id) || !($template_id>=0)) return false;
        $where=" WHERE ".$userpicturescolumn['uid']." = '" . (int)pnVarPrepForStore($uid) ."'
	    AND ".$userpicturescolumn['template_id']." = '". (int)pnVarPrepForStore($template_id) ."' ";
    }
    else $where=" WHERE ".$userpicturescolumn['id']." = '".(int)$picture_id."' ";

    // Get picture
    $sql = "SELECT ".$userpicturescolumn['filename'].",
                   ".$userpicturescolumn['comment'].",
                   ".$userpicturescolumn['id'].",
                   ".$userpicturescolumn['uid'].",
                   ".$userpicturescolumn['template_id'].",
                   ".$userpicturescolumn['verified']."
            FROM $userpicturestable
	    $where
	    ORDER BY ".$userpicturescolumn['position'].", ".$userpicturescolumn['filename']." DESC
	    ";
    $result =& $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        return false;
    }
    
    // if there is no picture uploaded take the default pic if there is one...
    $counter=$result->RowCount();
    if  ($counter==0) {
	    if ($args['code']==1) {
	    $tpl=pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$args['template_id']));
	    if ($tpl['defaultimage']!='') {
	        $item['code']='<img src="'.$tpl['defaultimage'].'" />';
	        $items[]=$item;
		    return $items;
	    }
	}
    }
    for (; !$result->EOF; $result->MoveNext()) {
        list($item['filename'], $item['comment'], $item['id'], $item['uid'], $item['template_id'], $item['verified']) = $result->fields;

	$filename = $prefix.$item['filename'];
	$item['filename']=$filename;
	$item['lastfilename']=$lastfilename;

	// if pictures have to be activated?
	unset($tpl);
	$show=true;
	$tpl =	pnModAPIFunc('UserPictures','admin','getTemplates',array(	'template_id'	=> $item['template_id']));
	if (($tpl['to_verify']==1) && ($item['verified']==0)) $show=false;
	if (pnUserGetVar('uid')==$item[uid]) $show=true;
	// now show if there's sth. to show
	if ($show) {
	    $code='<img alt="'.$item['comment'].'" title="'.$item['comment'].'" src="'.$item['filename'].'" />';
	    $code_thumbnail='<img alt="'.$item['comment'].'" title="'.$item['comment'].'" src="'.$item['filename'].'.thumb.jpg" />';
	}
	else if ($tpl['defaultimage'] != "") $code='<img src="'.$tpl['defaultimage'].'" />';
	else $code='';
	$item['code']=$code;
	$item['lastfilename']=$lastfilename;;
	$item['code_thumbnail']=$code_thumbnail;
	$item['tpl']=$tpl;

	// create thumbnails if they do not exist!
	UserPictures_userapi_createThumbnail(array('filename'=>$filename));
    
	// now we need to add the actual picture_id to the last array 
	// entry as next_id (this is for the position management important)
	$picture_id=$item['id'];
	$amount=count($items);
	if ($amount>0) {
	    $items[($amount-1)]['next_id']=$picture_id;
	}
	$items[]=$item;
	$lastfilename=$item['filename'];
    }

    $result->Close();
    // Return the number of items
    return $items;
}

/**
 * handle the uploaded picture
 *
 * @param	$args['uid']	
 * @param	$args['template_id']	
 * @param	$args['']	
 * @param	$args['']	
 * @return 	int		1 = sucessful
 *				2 = wrong extension / type of file
 *				3 = files to large
 *				4 = uploaded file has an error
 *				5 = resizing error
 *				6 = storepictureindb error
 */
function UserPictures_userapi_handleUploadedPicture($args)
{
    // some variables
    $template_id = 	$args['template_id'];
    $uid = 			$args['uid'];
    $prefix = 		pnModGetVar('UserPictures','datadir');

    // Here the function starts...
    $file =			FormUtil::getPassedValue('file');
    $filename =		$_FILES['file']['name'];
    $tempfile =		$_FILES['file']['tmp_name'];
    $filesize =		$_FILES['file']['size']/1024;
    $filetype =		$_FILES['file']['type'];

    // now check the extension
    $arr = 			explode(".",$filename);
    $ext =			strtolower($arr[count($arr)-1]);
    if (!preg_match('(gif|jpg|jpeg|png)',$ext)) return 2;
    $max_filesize =	pnModGetVar('UserPictures','maxfilesize');
    if ($filesize>$max_filesize) return 3;
    // the filename is <template_id>_<user_id>.<ext> timestamp included in filename for not having caching problems :)
    $addon='-'.time();

    $filename_noext = $prefix.$template_id.$addon.'_'.pnUserGetVar('uid');
    $filename =		$filename_noext.".".$ext;

    if ($_FILES['file']['error']!=0) return 4;

    // Move the uploaded file that is now stored in the temp folder to the right folder...
    $success =		move_uploaded_file($tempfile,$filename) or die(_USERPICTURESCOPYFAILURE.' tempfile '.$tempfile.' filename '.$filename);

    // convert jsut handles png and jpg-files. so gif hast to be converted to jpg before it can be resized
    if ($ext == "gif") {
		// source file
		$srcFile = $filename;
		// this comes out after converting
		$destFile = $filename_noext.'.jpg';
		// now let us convert the file using "convert" via shell execution
		$convert=pnModGetVar('UserPictures','convert');
		// the flatten option is to avoid problems with animated gif files
		$cmd="$convert $srcFile -flatten $destFile";
		shell_exec($cmd);
		// delete the gif source file
		unlink($srcFile);
		// set the new filename with jpg at the end because file is jpg now!
		$ext="jpg";
		$filename=$filename_noext.'.'.$ext;
    }
    // we now have a jpg or a png file... $filename and $filename_noext have a correct value now!
    $template =		pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$template_id));
    $max_height = 	$template['max_height'];
    $max_width = 	$template['max_width'];
    
    // resize the image
    if (!UserPictures_userapi_resizeImage(array(	'filename' 		=> $filename,
													'max_width'		=> $max_width,
													'max_height'	=> $max_height))) return 5;
    
    // let the API write it into the database that we now have a picture stored...
    if (!pnModAPIFunc('UserPictures','user','storePictureDB',array(	'template_id'		=> $template_id,
																	'filename'			=> $filename,
																	'comment' 			=> FormUtil::getPassedValue('comment')))) return 6;
    
    return false;
    // upload successfull...
    return 1;
}

/**
 * resize an image
 *
 * @param	$args['filename']	string
 * @param	$args['max_height']	int
 * @param	$args['max_width']	int
 * @return	bool
 */
function UserPictures_userapi_resizeImage($args) 
{
    $filename =		$args['filename'];
    $max_width =	$args['max_width'];
    $max_height =	$args['max_height'];
    $size =			$max_width."x".$max_height;
    return UserPictures_userapi_resizePicture(array(	'filename'			=> $filename,
														'size'				=> $size,
														'targetfilename'	=> $filename));
}
?>