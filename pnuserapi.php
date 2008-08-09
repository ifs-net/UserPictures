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
 * show picture
 *
 * This function shows a picture of a given template. If the user does not have a picture
 * uploaded for the given template the default picture will be returned. Module developers
 * who want to include UserPictures into their modules should use this function to include
 * a template image.
 *
 * @param	$args['template_id']
 * @return	string
 */
function UserPictures_userapi_showPicture($args)
{
  	// get parameters
  	$uid 			= (int)$args['uid'];
  	$template_id 	= (int)$args['template_id'];
  	if (!($template_id > 0) || (!($uid > 1))) return false;
	// get picture
  	$picture 		= UserPictures_userapi_get(array(
	  		'uid'			=> $uid,
	  		'template_id'	=> $template_id
		  ));
	$template = pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id' => $template_id));
	if (count($picture) == 0) return array (
			'code'			=> "<img src=\"".$template['defaultimage']."\" />",
			'template_id'	=> $template_id,
			'uid'			=> $uid,
			'uname'			=> pnUserGetVar('uname',$uid)
		);
	else return $picture[0];
}
 

/**
 * get fuction
 *
 * This function returns (all) pictures as an optional array with 
 * the following information:
 *  + information about the picture
 * 	+ associated categories
 *  + associated global category
 *  + associated persons
 *
 * @param	$args['uid']				int		filter: show pictures of a specified user only
 * @param	$args['template_id']		int		filter: show specific template
 * @param	$args['cat_id']				int		filter: show specific private category
 * @param	$args['globalcat_id']		int		filter: show specific global category
 * @param	$args['assoc_uid']			int		filter: show pictures associated to a given user
 * @param	$args['expand']				bool	additional information
 * @param	$args['countonly']			bool	only return number of pictures not an array
 * @param	$args['showmax']			int		number of images that should be shown on one page
 * @param	$args['startwith']			int		number of the image that should be the first on the page
 * @param	$args['managepicturelink']	int		set to 1 if manage own gallery link should be included in picture ur
 * @return 	array (pictures) or integer (with countonly parameter)
 * return array: // see pntables.php for more details and the userpictures core columns!
 * 			assoc_persons:		associated persons as array
 * 			category: 			private category information as array
 * 			code:				code to display existing picture
 * 			code_thumbnail:		code to dispay image as thumbnail including links, lighbox and overin
 * 			comment:			picture's comment
 * 			date:				picture's upload date as timestamp
 * 			filename:			picture's filename
 * 			filename_absolute:	absolute filename, but not including pngetaseurl()!
 * 			global_category:	global category information as array
 * 			id:					picture's id
 * 			template_id			picture's template id
 * 			thumb_url:			Link to the thumbnail overview
 * 			url:				Link to the picture in single dispay mode
 */
function UserPictures_userapi_get($args)
{
	// Get parameters
	$assoc_uid			= (int)	$args['assoc_uid'];
	$cat_id				= (int)	$args['cat_id'];
	$uid				= (int)	$args['uid'];
	$picture_id			= (int)	$args['id'];
	$globalcat_id 		= (int)	$args['globalcat_id'];
	$managepicturelink 	= (int)	$args['managepicturelink'];
	$template_id 		= 		$args['template_id'];
	$startwith			= 		$args['startwith'];
	if (!($startwith > 0)) $startwith = 1;
	$showmax			= 		$args['showmax'];
	$expand				= 		$args['expand'];
	if (!isset($expand) || !is_bool($expand)) $expand = true;
	if (isset($args['countonly'])) 	$countonly = true;
	else 							$countonly = false;
	
  	// Get database information
    $tables 					=& pnDBGetTables();
    $picturescolumn 			= &$tables['userpictures_column'];
    $personscolumn 				= &$tables['userpictures_persons_column'];
    $categoriescolumn 			= &$tables['userpictures_categories_column'];
    $globalcategoriescolumn 	= &$tables['userpictures_globalcategories_column'];
    $userscolumn 				= &$tables['userpictures_persons_column'];
    $templatescolumn 			= &$tables['userpictures_templates_column'];

	// Load global categories except when expand is false
	if ($expand) {
		$res = pnModAPIFunc('UserPictures','admin','getGlobalCategory');
		foreach ($res as $obj) {
		  	$id = $obj['id'];
		  	$globalCategoryArray[$id] = $obj;
		}
	}
	// Load private category if the filter is user-specific
	if ( ($expand) && (($uid > 0) || ($cat_id > 0))) {
		// load private categories
		$res = pnModAPIFunc('UserPictures','user','getCategory',array(
										'uid' 		=> $uid, 
										'cat_id' 	=> $cat_id));
		foreach ($res as $obj) {
		  	$id = $obj['id'];
		  	$categoryArray[$id] = $obj;
		}
	}

	// We'll built an array with all sql where parts we need and transform this array to a string later
	// The following where parts refer to the normal userpictures table. To make the columns unique we have to use the prefix tbl.
	if (strlen($template_id) == 0) $template_id = -99;	// Little hack to avoid troubles with a not specified template id
	if (isset($template_id) && ($template_id >= 0)) $whereArray['template_id'] 	= "tbl.".$picturescolumn['template_id']." = ".$template_id;
	if (isset($uid) 		&& ($uid > 0))	 		$whereArray['uid'] 			= "tbl.".$picturescolumn['uid']." = ".$uid;
	if (isset($cat_id) 		&& ($cat_id > 0)) 		$whereArray['cat_id']		= "tbl.".$picturescolumn['category']." = ".$cat_id;
	if (isset($globalcat_id)&& ($globalcat_id > 0))	$whereArray['globalcat_id'] = "tbl.".$picturescolumn['global_category']." = ".$globalcat_id;

	// Load common lib
	Loader::requireOnce('modules/UserPictures/pnincludes/common.php');

	// Table join information to join userpictures table with users table to retrieve the usernames
	// This join information is the second join information so we have to use the prefix a. in the following where parts
	$joinInfo[] = array (	'join_table'          =>  'users',			// table for the join
							'join_field'          =>  'uname',			// field in the join table that should be in the result with
                         	'object_field_name'   =>  'uname',			// ...this name for the new column
                         	'compare_field_table' =>  'uid',			// regular table column that should be equal to
                         	'compare_field_join'  =>  'uid');			// ...the table in join_table

	// Get the pictures now
	if ($assoc_uid > 1) {	
	  	// This part is an additional join information if the pictures associated with a specified user are requested
	  	// This join information is the second join information so we have to use the prefix b. in the following where parts
		$joinInfo[] = array (	'join_table'          =>  'userpictures_persons',	// table for the join
								'join_field'          =>  'picture_id',				// field in the join table that should be in the result with
                             	'object_field_name'   =>  'assoc_uid',				// ...this name for the new column
                             	'compare_field_table' =>  'id',						// regular table column that should be equal to
                             	'compare_field_join'  =>  'picture_id');			// ...the table in join_table
        $whereArray['assoc_uid'] 	= "b.".$personscolumn['assoc_uid']." = ".$assoc_uid;
		if (isset($template_id) && ($template_id >= 0)) $whereArray['template_id'] 	= "tbl.".$picturescolumn['template_id']." = 0";
		if (isset($uid) && ($uid > 0)) $whereArray['uid'] = "tbl.".$picturescolumn['uid']." = ".$uid;
	}
	
	// Now select priority for order statement. If the pictures of only one user should be retrieved 
	// we'll take position as sort order criteria. Otherwise use id (latest picture shown first)
	if ($uid > 1) $order = "tbl.".$picturescolumn['position']." asc, tbl.".$picturescolumn['date']." desc, tbl.".$picturescolumn['id']." desc";
	else $order = "tbl.".$picturescolumn['id']." desc";
	
	// Construct where statement now
	$where = up_constructWhere($whereArray);

	// Return numer of pictures found if countonly is set
	if ($countonly) {
	  	$res = DBUtil::selectExpandedObjectCount('userpictures',$joinInfo,$where);
	  	if (($picture_id > 0) && (count($res)>0)) return 1;
	  	if (!$res) return 0;
	  	else return $res;
	}
	// Otherwise get object array with one or more requested picture(s)
	else if ($picture_id > 0) {
	  	$obj = DBUtil::selectExpandedObjectByID('userpictures',$joinInfo,"tbl.".$picture_id);
	  	$objArray[] = $obj;
	}
	else $objArray = DBUtil::selectExpandedObjectArray('userpictures',$joinInfo,$where,$order,($startwith-1),$showmax);

	// Get additional information 
    $datadir = pnModGetVar('UserPictures','datadir');

	// build result array
	$counter 	= $startwith;	// we need this counter to get the right links from lightbox to add comments etc.
	if ($counter < 0) $counter = 0;
	$res 		= array();
	foreach ($objArray as $obj) {
	  	// Add absolute filename to object
	  	$obj['filename_absolute']=$datadir.$obj['filename'];
		// Create thumbnails if they do not exist!
		if (!UserPictures_userapi_createThumbnail(array('filename'=>$obj['filename_absolute']))) LogUtil::registerError(_USERPICTURESTHUMBNAILCREATIONERROR);

		// Construct view array for thumbnail to create the regular non JS link
		$viewarray['singlemode']	= 1;
		if ($template_id >= 0) 	$viewarray['template_id'] 	= $template_id;
		if ($uid >= 0) 			$viewarray['uid'] 			= $uid;
		if ($assoc_uid >= 0) 	$viewarray['assoc_uid'] 	= $assoc_uid;
		if ($cat_id >= 0) 		$viewarray['cat_id']		= $cat_id;
		if ($globalcat_id >= 0) $viewarray['globalcat_id']	= $globalcat_id;
								$viewarray['upstartwith']	= $counter;	
		if (isset($managepicturelink) && ($managepicturelink > 0)) $viewarray['managepicturelink'] = 1;
		// create the link to the singe picture
		$obj['thumb_url']	= pnModURL('UserPictures','user','view',$viewarray);
		// create the link to the singe picture
		$viewarray['singlemode']	= 1;
		$obj['url']	= pnModURL('UserPictures','user','view',$viewarray);
		// Add additional information if requested
		if ($expand) {	
			// Add private category
		  	$category = $obj['category'];
		  	if ($category > 0) $obj['category'] = $categoryArray[$category];
		  	else unset($obj['category']);

			// Add global category
		  	$global_category = $obj['global_category'];
		  	if ($global_category > 0) $obj['global_category'] = $globalCategoryArray[$global_category];
		  	else unset($obj['global_category']);

		  	// Add associated persons
		  	$persons = UserPictures_userapi_getPersons(array('picture_id' => $obj['id']));
		  	if (count($persons) > 0) $obj['assoc_persons'] = $persons;

			// Prepare thumbnail information as string
			$info = _USERPICTURESOWNER.": ".$obj['uname']."<br />";
			if ($obj['comment'] != '') 					$info.=_USERPICTURESCOMMENT.": ".$obj['comment']."<br />";
			if ($obj['date'] != '0000-00-00 00:00:00') 	$info.=_USERPICTURESUPLOADDATE.": ".$obj['date']."<br />";
			if ($obj['category']['title'] != '') 		$info.=_USERPICTURESPRIVATECATEGORY.": ".$obj['category']['title']."<br />";
			if ($obj['global_category']['title'] != '')	$info.=_USERPICTURESGLOBALCATEGORY.": ".$obj['global_category']['title']."<br />";
			unset($assoc_string);
			if (count($obj['assoc_persons']) > 0) {
			  	$assoc_string=_USERPICTURESPERSONSLINKEDHERE.": ";
				$c=1;
			  	foreach ($obj['assoc_persons'] as $p) {
				    $assoc_string.=$p['assoc_uname'];
				  	if ($c!=count($obj['assoc_persons'])) $assoc_string.=", ";
				  	else $assoc_string.=".";
				  	$c++;
				}
			}
			$infobox 	= pnRender::getInstance('UserPictures');
			$title 		= $info.$assoc_string.'<br /><a href="'.$obj['url'].'">'._USERPICTURESVIEWCOMMENTSANDASSOCS.'</a>';
			$info 		= '<div style="text-align:left;border: 1px dotted #000;">'.$info.$assoc_string.'</div>';
	  	}

		// Add code part
		$obj['code_thumbnail'] 	= '<a 	id="p'.$obj['id'].'" javascript:void(0);" 
										onmouseover="return overlib(\''.up_prepDisplay($info).'\')" 
										onmouseout="return nd();" 
										href="'.pnModURL('UserPictures','user','view',$viewarray).'" 
										rel="lightbox[set]"><img id="pt'.$obj['id'].'" class="userpictures_photo" 
										src="'.$obj['filename_absolute'].'.thumb.jpg" /></a>
									<script type="text/javascript">
										$(\'p'.$obj['id'].'\').href="'.$obj['filename_absolute'].'";
										$(\'p'.$obj['id'].'\').title="'.str_replace('"','\"',$title).'";
										$(\'pt'.$obj['id'].'\').title=" ";
									</script>';

		$obj['code'] 			= '<img title="'.pnVarPrepForDisplay($obj['comment']).' " class="userpictures_photo" src="'.$obj['filename_absolute'].'" />';

		// Increase counter to have the upstartwith-variable with the right values
	  	$counter++;
	  	// Add picture array to result array
		$res[] = $obj;
	}

	// Return result array
	return $res;
}

/**
 * get latest images
 *
 * This function returns the latest picture uploads
 *
 * @param	$args['template_id']	(opt)	int		template id
 * @param	$args['numrows']				int		pics per row
 * @param	$args['numcols']				int		pics per columns
 * @return	output
 */
function UserPictures_userapi_latest($args)
{
  	$numrows = (int) $args['numrows'];
  	$numcols = (int) $args['numcols'];
  	$showmax = $numcols * $numrows;
  	if (!($showmax > 0)) return _USERPICTURESWRONGPARAMETERS;
	// load handler class
	Loader::includeOnce('modules/UserPictures/pninclides/common.php');
	$pictures 	= UserPictures_userapi_get(array(
			'template_id' 	=> $args['template_id'],
			'showmax'		=> $showmax
		));

	// Add overlib
    PageUtil::addVar('javascript','javascript/overlib/overlib.js');

	// get render instance and create output
	$render = pnRender::getInstance('UserPictures');
	$render->assign('thumbnailheight',		up_getThumbnailHeight());
	$render->assign('nopager',				1);
	$render->assign('cycle',				up_getCycle($numcols));
	$render->assign('pictures',				$pictures);
	$render->assign('ezcommentsavailable',	pnModAvailable('EZComments'));
    return $render->fetch('userpictures_user_viewsimpleinclude.htm');
}

/**
 * Add order link to pictures
 *
 * This function extends the picture array and includes the sort list if the user
 * wants to change the posision of the pictures and has javascript disabled. The
 * list has to be included into the "move up" and "move down" form at the picture
 * management page.
 *
 * @param	$args['pictures']		array
 * @return	array
 */
function UserPictures_userapi_addOrderLinkToPictures($args)
{
  	// Include external functions
	Loader::requireOnce('modules/UserPictures/pnincludes/common.php');
	
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
 * associate a picture with a global category
 *
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_setGlobalCategory($args)
{
  	// get parameters
    $picture_id = (int)$args['picture_id'];
    $cat_id 	= (int)$args['cat_id'];
    $uid 		= (int)$args['uid'];
    // some checks
    if (!isset($cat_id) || (!($cat_id>=0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
  	// get picture object
 	$obj = DBUtil::selectObjectByID('userpictures',$picture_id);
 	// little security check
 	if ($uid != $obj['uid']) return false;
 	// add category
 	$obj['global_category'] = (int)$args['cat_id'];
 	// update object and return success
 	return DBUtil::updateObject($obj,'userpictures');
} 

/**
 * delete an assoziation from a global category
 *
 * @param	$args['picture_id']	int
 * @param	$args['uid']		int
 * @return	bool
 */
function UserPictures_userapi_delFromGlobalCategory($args)
{
  	$args['cat_id'] = 0;
    return UserPictures_userapi_setGlobalCategory($args);
} 

/**
 * associate a picture with a private category
 *
 * @param	$args['uid']		int
 * @param	$args['picture_id']	int
 * @param	$args['cat_id']		int
 * @return	bool
 */
function UserPictures_userapi_setCategory($args)
{
  	// get parameters
    $picture_id = (int)$args['picture_id'];
    $cat_id 	= (int)$args['cat_id'];
    $uid 		= (int)$args['uid'];
    // some checks
    if (!isset($cat_id) || (!($cat_id>=0))) return false;
    if (!isset($picture_id) || (!($picture_id>0))) return false;
  	// get picture object
 	$obj = DBUtil::selectObjectByID('userpictures',$picture_id);
 	// little security check
 	if ($uid != $obj['uid']) return false;
 	// add category
 	$obj['category'] = (int)$args['cat_id'];
 	// update object and return success
 	return DBUtil::updateObject($obj,'userpictures');
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
  	$args['cat_id'] = 0;
    return UserPictures_userapi_setCategory($args);
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
		// todo - änderungen wegen neuer tabellenstruktur
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
 * get a category
 *
 * @param	$args['cat_id']	int
 * @return	array
 */
function UserPictures_userapi_getCategory($args)
{
    $uid	= (int) $args['uid'];
  	$id 	= (int) $args['cat_id'];
  	$res	= null;
  	if ($id > 0) $res[] = DBUtil::selectObjectByID('userpictures_categories',$id);
  	else if ($uid > 1) {
	    $pntable =& pnDBGetTables();
	    $userpictures_categoriescolumn = &$pntable['userpictures_categories_column'];
	   	$where = $userpictures_categoriescolumn['uid']." = ".$uid;
	    $orderby 	= $userpictures_categoriescolumn['title'];
	    return DBUtil::selectObjectArray('userpictures_categories',$where,$orderby);
	}
	return $res;
}

/**
 * delete an associaiton to a picture
 *
 * @param	$args['id']		int
 * @return	bool
 */
function UserPictures_userapi_delPerson($args)
{
    $id	= (int)$args['id'];
    if (!isset($id) || (!($id>0))) return false;
    
    // Get db table array
	$obj = DBUtil::selectObjectByID('userpictures_persons',$id);
	if (($obj['uid'] == pnUserGetVar('uid')) || ($obj['assoc_uid'] == pnUserGetVar('uid'))) return DBUtil::deleteObject($obj,'userpictures_persons');
	return false;	// otherwise
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

	// construct join    
    $joinInfo[] = array (
    		'join_table'			=> 'users',	// table to join with
    		'join_field'			=> 'uname',	// field that should be in the result
    		'object_field_name'		=> 'uname',	// how the field should be named in the array
    		'compare_field_table'	=> 'uid',	// connection to join
    		'compare_field_join'	=> 'uid'	// field that should be equal in the join
		);
    $joinInfo[] = array (
    		'join_table'			=> 'users',	// table to join with
    		'join_field'			=> 'uname',	// field that should be in the result
    		'object_field_name'		=> 'assoc_uname',	// how the field should be named in the array
    		'compare_field_table'	=> 'assoc_uid',	// connection to join
    		'compare_field_join'	=> 'uid'	// field that should be equal in the join
		);
    $where = $userpictures_personscolumn['picture_id']." = ". (int)$args['picture_id'];
    return DBUtil::selectExpandedObjectArray('userpictures_persons',$joinInfo,$where);
}

/**
 * rotate a picture
 *
 * @param	$args['uid']			int
 * @param	$args['angle']			int
 * @param	$args['picture_id']		int
 * @return	bool
 */
function UserPictures_userapi_rotatePicture($args)
{
  	// get parameters
  	$uid			= (int) $args['uid'];
  	$angle			= (int) $args['angle'];
  	$picture_id		= (int) $args['picture_id'];
  	
  	// get picture and check the owner first
  	$obj = DBUtil::selectObjectByID('userpictures',$picture_id);
	if ($obj['uid'] != $uid) return false;
	
	// delete old thumbnail; new is generated on demand later
	$datadir		= pnModGetVar('UserPictures','datadir');
	$image			= $datadir.$obj['filename'];
	$filename_new	= $obj['template_id'].'-'.time().'_'.$uid.'.jpg';
	$image_new		= $datadir.$filename_new;
	$thumbnail		= $image.'.thumb.jpg';
	if (file_exists($thumbnail)) unlink($thumbnail);

	// read source image, rotate it and store result
	if (imagejpeg(imagerotate(imagecreatefromjpeg($image), $angle, 0),$image_new,100)) {
	  	$obj['filename'] = $filename_new;
	  	return DBUtil::updateObject($obj,'userpictures');
	}
	else return false;
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
 * This function is neccesary for the avatar management and 
 * copies an existing picture into zikula's avatar directory 
 * and activates it as actual avatar
 *
 * @param	$args['uid']			int
 * @param	$args['picture_id']		int
 * @return	bool
 */
function UserPictures_userapi_setAvatar($args)
{
   // get data and verify data
    $uid 			= $args['uid'];
    $picture_id 	= $args['picture_id'];
    
    // We will now resize the image and copy it into the postnuke avatar folder (images/avatar as standard!)
    $picture		= UserPictures_userapi_get(array('id' => $picture_id));
    $picture		= $picture[0];
    
    // security check
    if ($picture['uid'] != $uid) return false;
    
    $targetfilename	= pnModGetVar('UserPictures','avatardir')."/pers_".$uid.".jpeg";
    
    // if a avatar with this name exists we have to delete it!
    if (file_exists($targetfilename)) unlink($targetfilename);

    // create the image
    if (!UserPictures_userapi_resizePicture(array(
			'filename'			=> $picture['filename_absolute'],
			'size'				=> pnModGetVar('UserPictures','avatarsize'),
			'targetfilename'	=> $targetfilename))) return false;

    // Set the user's variable
    if (pnUserSetVar('_YOURAVATAR','pers_'.$uid.'.jpeg')) return true;
    else return false;
}

/**
 * create a resized picture
 *
 * @param	$args['filename']		string
 * @param	$args['size']			string
 * @param	$args['targetfilename']	string
 */
function UserPictures_userapi_resizePicture($args)
{
    $convert 			= pnModGetVar('UserPictures','convert');
    $thumbnailcreation 	= pnModGetVar('UserPictures','thumbnailcreation');
    $size				= $args['size'];
    $filename			= $args['filename'];
    $targetfilename		= $args['targetfilename'];
    if (!isset($targetfilename) || ($targetfilename == '')) $targetfilename = $filename;
    if ($args['hint']) 	$hint=1;
    else 				$hint=0;
    
    if ($thumbnailcreation == 'gdlib') {
		$s 		= explode('x',$size);
		$w1 	= (int)$s[0];
		$w2 	= (int)$s[1];
		if ($w1 > $w2) $longside = $w1;
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
    $uid 			= $args['uid'];
    $template_id 	= $args['template_id'];
    $picture_id 	= $args['picture_id'];
    if (!($uid>0)  || !($template_id>(-1) || !($picture_id>0))) return false;

    // Get picture object 
    $picObj = DBUtil::selectObjectByID('userpictures',$picture_id);
    if ($picObj['uid'] != $uid) {
	  	LogUtil::registerError(_USERPICTURESDELETEONLYOWNPICTURES);
	  	return false;
	}

    // delete all associated persons
    $dummy = UserPictures_userapi_getPersons(array('picture_id'=>$picture_id));
    foreach ($dummy as $assoc) if (!UserPictures_userapi_delPerson(array('picture_id'=>$picture_id,'uname'=>$assoc['uname']))) return false;
    
    // get the picture's filename to delete it
    if (!unlink(pnModGetVar('UserPictures','datadir').$picObj['filename'])) return false;
    unlink(pnModGetVar('UserPictures','datadir').$picObj['filename'].'.thumb.jpg');

	// and delete the picture object
	return DBUtil::deleteObject($picObj,'userpictures');
}

/**
 * storePictureDB
 * 
 * @return   bool
 */
function UserPictures_userapi_storePictureDB($args)
{
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
			'filename'		=> $filename,
			'date'			=> date("Y-m-d H:i:s",time()),
			'position'		=> ((int)UserPictures_userapi_get(array(
					'uid'		=> pnUserGetVar('uid'),
					'countonly'	=> true
					))*(-1))
		);
	DBUtil::insertObject($obj,'userpictures');
	return true;
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
	if (!(file_exists($args[filename].'.thumb.jpg'))) return UserPictures_userapi_resizePicture(
		array(	
			'filename'			=> $args['filename'],
			'size'				=> pnModGetVar('UserPictures','thumbnailsize'),
			'targetfilename'	=> $args['filename'].'.thumb.jpg',
			'hint'				=> pnModGetVar('UserPictures','hint')
			));
	else return true;
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
    $template_id 	= $args['template_id'];
    $uid 			= pnUserGetVar('uid');
    $prefix 		= pnModGetVar('UserPictures','datadir');

    // Get file and file information
    $file 			= FormUtil::getPassedValue('file');
    $filename 		= $_FILES['file']['name'];
    $tempfile 		= $_FILES['file']['tmp_name'];
    $filesize 		= $_FILES['file']['size']/1024;
    $filetype 		= $_FILES['file']['type'];

    // now check the extension
    $arr 			= explode(".",$filename);
    $ext 			= strtolower($arr[count($arr)-1]);
    
	// is the type of the file correct?
    if (!preg_match('(gif|jpg|jpeg|png)',$ext)) return 2;
    
    // is the file too big?
    $max_filesize 	= pnModGetVar('UserPictures','maxfilesize');
    if ($filesize > $max_filesize) return 3;

    // the filename is <datadir>/<template_id>_<user_id>.<ext> timestamp included in filename for not having caching problems :)
	$filename_noext = $prefix.$template_id.'-'.time().'_'.$uid;
    $filename 		= $filename_noext.".".$ext;

	// did an upload error occur?
    if ($_FILES['file']['error']!=0) return 4;

    // Move upload from temp to target folder
    $success 		= move_uploaded_file($tempfile,$filename) or die("FATAL ERROR! "._USERPICTURESCOPYFAILURE.' tempfile '.$tempfile.' filename '.$filename);

    // convert just handles png and jpg-files. so gif hast to be converted to jpg before it can be resized
    if ($ext == "gif") {
		$srcFile 	= $filename;								// source file
		$destFile 	= $filename_noext.'.jpg';					// this comes out after converting
		$convert	= pnModGetVar('UserPictures','convert');	// get convert path
		$cmd		= "$convert $srcFile -flatten $destFile";	// add flatten option to avoid animated gifs
		shell_exec($cmd);										// execute command via shell_exec
		unlink($srcFile);										// old gif file
		$ext		= "jpg";									// update extension
		$filename	= $filename_noext.'.'.$ext;					// update filename
    }
    
    // we now have a jpg or a png file...
    $template 		= pnModAPIFunc('UserPictures','admin','getTemplates',array('template_id'=>$template_id));
    $max_height 	= $template['max_height'];
    $max_width 		= $template['max_width'];
    
    // resize the image
    if (!UserPictures_userapi_resizePicture(array(	'filename' 	=> $filename,
													'size'		=> $max_width."x".$max_height))) return 5;
    
    // let the API write it into the database that we now have a picture stored...
    if (!pnModAPIFunc('UserPictures','user','storePictureDB',array(	'template_id'		=> $template_id,
																	'filename'			=> $filename,
																	'comment' 			=> FormUtil::getPassedValue('comment')))) return 6;
    // upload successfull...
    return 1;
}
?>