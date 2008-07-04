<?php

/**
 * find orphan pictures
 *
 * @param	$args[delete]	int
 */
function UserPictures_adminapi_getOrphanPictures($args)
{
    $templates = UserPictures_adminapi_getTemplates();
    // Get DB Setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    // Get Tables
    $userpicturestable = &$pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];
    
    $templates[]=array('id'=>0);
    
    foreach ($templates as $template) {
	$pictures = pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template[id]));
	foreach ($pictures as $picture) {
	    $email=pnUserGetVar('email',$picture['uid']);
	    if (!(strlen($email)>0)) {
		$pic['picture_id']=$picture['id'];
		$pic['uid']=$picture['uid'];
		$pic['template_id']=$picture['template_id'];
		$pics[]=$pic;
		if ($args[delete]=="1") {
		    pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$pic['picture_id'],'template_id'=>$pic['template_id'],'uid'=>$pic['uid']));
		}
	    }
        }
    }
    return $pics;
}

/**
 * find orphan DB entries
 *
 * @param	$args[delete]	int
 */
function UserPictures_adminapi_getOrphanDBFiles($args)
{
    $templates = UserPictures_adminapi_getTemplates();
    // Get DB Setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    // Get Tables
    $userpicturestable = &$pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];
    
    $templates[]=array('id'=>0);
    
    foreach ($templates as $template) {
	$pictures = pnModAPIFunc('UserPictures','user','getPictures',array('template_id'=>$template[id]));
	foreach ($pictures as $picture) {
	    if (!file_exists($picture[filename])) {
		unset($file);
		$file[filename]=$picture[filename];
		$file[uid]=$picture[uid];
		$files[]=$file;
		if ($args[delete]=="1") {
		    $f=explode("/",$picture[filename]);
		    $filename=$f[(count($f)-1)];
    		    $sql="DELETE FROM $userpicturestable WHERE $userpicturescolumn[filename] = '". pnVarPrepForStore($filename)  ."' LIMIT 1";
		    $dbconn->Execute($sql);
		}
	    }
	}
    }
    return $files;
}

/**
 * find orphan files in the filesystem
 *
 * @param	$args[delete]	int
 */
function UserPictures_adminapi_getOrphanFiles($args)
{
    $prefix=pnModGetVar('UserPictures','datadir');
    $path=$prefix;
    $verz=opendir($path);

    // Get DB Setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    // Get Tables
    $userpicturestable = &$pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];

    while ($file=readdir($verz)) {
	if ((filetype($path.$file)!="dir") && ($file != "..")  && ($file != ".") && ($file!="index.html") && ($file!="index.htm") && ($file!=".htaccess") 
	    // and now check for thumbnails that are correct
	    ){
	    $sql = "SELECT $userpicturescolumn[id] 
		    FROM $userpicturestable
        	    WHERE $userpicturescolumn[filename] = '".$file."'";
	    $result=$dbconn->Execute($sql);
	    if ($dbconn->ErrorNo() == 0) {
		$counter=$result->RecordCount();
		if ($counter == 0) {
    	    
		    // there is no entry in the Database.
		    // we must see if the entry is a valid thumbnail
		    // thumbnails are not stored in the database.
		    $thumbnail=false;
		    if (eregi('thumb.jpg',$file)) {
			unset($orgfile);
    			$orgfile = $prefix.substr($file,0,(strlen($file)-strlen('thumb.jpg')-1));
			if (file_exists($orgfile)) $thumbnail=true;
		    }
		    if (!$thumbnail) {
			unset($item);
			$item[filename]=$file;
			if ($args[delete]=="1") {
		    	    if (unlink($path.'/'.$file)) $item[deleted]=1;
			}
		    $items[]=$item;
		    }
	        }
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
    $prefix=pnModGetVar('UserPictures','datadir');
    $path=$prefix;
    $verz=opendir($path);
    $c=0;
    while ($file=readdir($verz)) {
	if ((filetype($path.$file)!="dir") && ($file != "..")  && ($file != ".") && ($file!="index.html") && ($file!="index.htm") && ($file!=".htaccess")){
	    if (eregi('thumb',$file)) {
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
    $prefix=pnModGetVar('UserPictures','datadir');
    $path=$prefix;
    $verz=opendir($path);
    while ($file=readdir($verz)) $i++;
    return $i;
}


/**
 * create a new UserPictures item
 * 
 * @return   int              UserPictures item ID on success, false on failure
 */
function UserPictures_adminapi_storeTemplate($args)
{

    // Get DB Setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();
    // Get Tables
    $userpictures_templatestable  = &$pntable['userpictures_templates'];
    $userpictures_templatescolumn = &$pntable['userpictures_templates_column'];

    list(	$id,
		$title,
		$max_width,
		$max_height,
		$defaultimage,
		$to_verify	) = pnVarPrepForStore(	$args[id],
							$args[title],
							$args[max_width],
							$args[max_height],
							$args[defaultimage],
							$args[to_verify]	);
	
    // Add it now
    if ($id>0) $sql= "UPDATE $userpictures_templatestable
		SET $userpictures_templatescolumn[title] = '". $title ."',
		$userpictures_templatescolumn[max_width] = '". $max_width ."',
		$userpictures_templatescolumn[max_height] = '". $max_height ."',
		$userpictures_templatescolumn[defaultimage] = '". $defaultimage ."',
		$userpictures_templatescolumn[to_verify] = '". $to_verify ."'
		WHERE $userpictures_templatescolumn[id] = '". (int)$id ."'
		";
    else   $sql = "INSERT INTO $userpictures_templatestable (
              $userpictures_templatescolumn[title],
              $userpictures_templatescolumn[max_width],
              $userpictures_templatescolumn[max_height],
              $userpictures_templatescolumn[defaultimage],
              $userpictures_templatescolumn[to_verify]	)
            VALUES (
              '".$title."',
              '".(int)$max_width."',
              '".(int)$max_height."',
              '".$defaultimage."',
              '".(int)$to_verify."'	
              )";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }
    return true;
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
	$item[to_verify]=0;
	return $item;
    }
    
    $sql = "SELECT 	$userpictures_templatescolumn[id],
			$userpictures_templatescolumn[title],
			$userpictures_templatescolumn[max_width],
			$userpictures_templatescolumn[max_height],
			$userpictures_templatescolumn[defaultimage],
			$userpictures_templatescolumn[to_verify]
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
		$item[defaultimage],
		$item[to_verify]	) = $result->fields;
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
 * activate a picture
 * 
 * @param    $args[uid]     		int
 * @param    $args[template_id]		template_id
 * @return   bool             true on success, false on failure
 */
function UserPictures_adminapi_activatePicture($args)
{
    // Security 
    if (!pnSecAuthAction(0, 'UserPictures::', "::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _MODULENOAUTH);
        return false;
    }

    // Get datbase setup
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // Let the calling process know that we have finished successfully
    return true;
}

/**
 * deletePicture
 *
 * @param	$args[picture_id]	int
 * @return	bool
 */
function UserPictures_adminapi_deletePicture($args)
{
    // get data and verify data
    $picture_id = $args[picture_id];
    if (!($picture_id>0)) return false;

    // get the picture's filename to delete it
    $picArray=pnModAPIFunc('UserPictures','user','getPicture',array('picture_id'=>$picture_id));
    $picture=$picArray[0];
    $filename=$picture[filename];
    
    // now unlink the file...
    $res=unlink($filename);
    if (!$res) return false;

    // Get datbase setup 
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // get Tables
    $userpicturestable = $pntable['userpictures'];
    $userpicturescolumn = &$pntable['userpictures_column'];

    // SQL statement 
    $sql = "DELETE FROM $userpicturestable 
	    WHERE  $userpicturescolumn[id] = '". (int)$picture_id ."'
	    LIMIT 1 ";
    $result = $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) return false;

    // set should be closed when it has been finished with
    $result->Close();

    return true;
}


?>