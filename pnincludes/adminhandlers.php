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
		$render->assign('items_thumbnailcreation',	$items_thumbnailcreation);
		$render->assign('activated',				pnModGetVar('UserPictures','activated'));
		$render->assign('avatarmanagement',			pnModGetVar('UserPictures','avatarmanagement'));
		$render->assign('convert',					pnModGetVar('UserPictures','convert'));
		$render->assign('avatarsize',				pnModGetVar('UserPictures','avatarsize'));
		$render->assign('ownuploads',				pnModGetVar('UserPictures','ownuploads'));
		$render->assign('maxfilesize',				pnModGetVar('UserPictures','maxfilesize'));
		$render->assign('disabledtext',				pnModGetVar('UserPictures','disabledtext'));
		$render->assign('maxwidth',					pnModGetVar('UserPictures','maxwidth'));
		$render->assign('maxheight',				pnModGetVar('UserPictures','maxheight'));
		$render->assign('thumbnailsize',			pnModGetVar('UserPictures','thumbnailsize'));
		$render->assign('thumbnailcreation',		pnModGetVar('UserPictures','thumbnailcreation'));
		$render->assign('datadir',					pnModGetVar('UserPictures','datadir'));
		$render->assign('hint',						pnModGetVar('UserPictures','hint'));
		$render->assign('verifytext',				pnModGetVar('UserPictures','verifytext'));
		return true;
    }
    function handleCommand(&$render, &$args)
    {
		if ($args['commandName']=='update') {
		    if (!$render->pnFormIsValid()) return false;
		    $obj = $render->pnFormGetValues();
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