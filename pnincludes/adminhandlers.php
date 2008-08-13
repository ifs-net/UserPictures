<?php
/**
 * Handler for main administration management
 */
class userpictures_admin_mainHandler
{
    function initialize(&$render)
    {
      	// assign existing categories
		$items_thumbnailcreation = array (	
								array('text' => _USERPICTURESTCCONVERT, 'value' => 'convert'),
								array('text' => _USERPICTURESTCGDLIB, 	'value' => 'openlayers')
								);
		$data = array(
			'items_thumbnailcreation'	=> $items_thumbnailcreation,
			'activated'					=> pnModGetVar('UserPictures','activated'),
			'avatarmanagement'			=> pnModGetVar('UserPictures','avatarmanagement'),
			'convert'					=> pnModGetVar('UserPictures','convert'),
			'avatarsize'				=> pnModGetVar('UserPictures','avatarsize'),
			'ownuploads'				=> pnModGetVar('UserPictures','ownuploads'),
			'maxfilesize'				=> pnModGetVar('UserPictures','maxfilesize'),
			'disabledtext'				=> pnModGetVar('UserPictures','disabledtext'),
			'maxwidth'					=> pnModGetVar('UserPictures','maxwidth'),
			'maxheight'					=> pnModGetVar('UserPictures','maxheight'),
			'thumbnailsize'				=> pnModGetVar('UserPictures','thumbnailsize'),
			'thumbnailcreation'			=> pnModGetVar('UserPictures','thumbnailcreation'),
			'datadir'					=> pnModGetVar('UserPictures','datadir'),
			'avatardir'					=> pnModGetVar('UserPictures','avatardir'),
			'hint'						=> pnModGetVar('UserPictures','hint'),
			'verifytext'				=> pnModGetVar('UserPictures','verifytext')
				);
		$render->assign($data);
		return true;
    }
    function handleCommand(&$render, &$args)
    {
		if ($args['commandName']=='update') {
		    if (!$render->pnFormIsValid()) return false;
		    $obj = $render->pnFormGetValues();
		    // store everything that is submitted in $obj as module variable
		  	foreach ($obj as $key=>$val) pnModSetVar('UserPictures',$key,$val);
		  	LogUtil::registerStatus(_USERPICTURESSTORED);
		}
		return true;
    }
}

/**
 * Handler for global category administration management
 */
class userpictures_admin_categoriesHandler
{
    function initialize(&$render)
    {
      	// assign existing categories
      	$render->assign('categories',DBUtil::selectObjectArray('userpictures_globalcategories'));
      	$cat_id = (int)FormUtil::getPassedValue('cat_id');
      	if ($cat_id > 0) {
      	  	$act = DBUtil::selectObjectByID('userpictures_globalcategories',$cat_id);
      	  	if (is_array($act) && ($act['id']>0)) {
				  $render->assign($act);
				  $this->id = $act['id'];
			}
		return true;
    	}
    }
    function handleCommand(&$render, &$args)
    {
		if ($args['commandName']=='update') {
		  	// otherwise proceed
		    if (!$render->pnFormIsValid()) return false;
		    $obj = $render->pnFormGetValues();
			if ($this->id > 0) $obj['id']=$this->id;
			// delete
			if (FormUtil::getPassedValue('delete')==1) {
			  	if (pnModAPIFunc('UserPictures','admin','delGlobalCategory',array('id' => $obj['id'])));
			}
			else {	// or create/update?
			    if ($obj['id']>0) 	$res = DBUtil::updateObject($obj,'userpictures_globalcategories');
			    else 				$res = DBUtil::insertObject($obj,'userpictures_globalcategories');
			    if ($res) LogUtil::registerStatus(_USERPICTURESACTIONDONE);
			    else LogUtil::registerError(_USERPICTURESERRORSAVING);
			}
			return pnRedirect(pnModURL('UserPictures','admin','categories'));
		}
		return true;
    }
}
?>