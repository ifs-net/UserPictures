<?php
 
/**
 * This file is a skeleton file that can be adopted by every module developer
 */
 
/**
 * Delete a user in the module "UserPictures"
 * 
 * @param	$args['uid']	int		user id
 * @return	array   
 */
function UserPictures_userdeletionapi_delUser($args)
{
  	$uid = $args['uid'];
	if (!pnModAPIFunc('UserDeletion','user','SecurityCheck',array('uid' => $uid))) {
	  	$result 	= _NOTHINGDELETEDNOAUTH;
	}
	else {
	  	// Here you should write your userdeletion routine.
	  	// Delete your database entries or anonymize them.
		$result				= "";
	    $tables 			=& pnDBGetTables();
	    $personscolumn 		= &$tables['userpictures_persons_column'];
	    $categoriescolumn	= &$tables['userpictures_categories_column'];
		// 1. delete all associations to and from $uid
	 	// get db table array
		$where 			= $personscolumn['uid']." = ".$uid." OR ".$personscolumn['assoc_uid']." = ".$uid;
		$objArray		= DBUtil::selectObjectArray('userpictures_persons',$where);
		foreach ($objArray as $obj) DBUtil::deleteObject($obj,'userpictures_persons');
		$result.= count($objArray)." "._USERPICTURESASSOCSBOTHSIDES.", ";

		// 2. delete all pictures
		$pictures = pnModAPIFunc('UserPictures','user','get',array('uid' => $uid));
		foreach ($pictures as $p) pnModAPIFunc('UserPictures','user','deletePicture',array('uid' => $uid, 'picture_id' => $p['id']));
		$result.= count($pictures)." "._USERPICTURESPICTURES." "._USERPICTURESAND." ";

		// 3. delete all private categories from $uid
		$where 			= $categoriescolumn['uid']." = ".$uid;
		$objArray		= DBUtil::selectObjectArray('userpictures_categories',$where);
		foreach ($objArray as $obj) DBUtil::deleteObject($obj,'userpictures_categories');
		$result.= count($objArray)." "._USERPICTURESCATEGORIES." "._USERPICTURESFORUSER." ";

		$result.= pnUserGetVar('uname',$uid);
	}
	return array(
			'title' 	=> _USERPICTURESUSERPICTURETITLE,
			'result'	=> $result

		);
}
?>