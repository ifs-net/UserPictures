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
 * UserPicture needle
 *
 * @param $args['nid'] 	int	(needle id = picture id)
 *                      int - int (picture id and key separated with "-" character)
 * @return array()
 */
function UserPictures_needleapi_userpicture($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    $nidArray = explode('-',$nid);
    if (count($nidArray) == 2) {
      	$nid = (int)$nidArray[0];
      	$key = (int)$nidArray[1];
	}
	else $nid = (int)$nid;

    // Load language
    pnModLangLoad('UserPictures','user');
    
    // Load module StyleSheet
	PageUtil::AddVar('stylesheet', ThemeUtil::getModuleStylesheet('UserPictures'));

    // We need to add some page vars
    $scripts[] = 'javascript/ajax/prototype.js';
	$scripts[] = 'javascript/ajax/pnajax.js';
    $scripts[] = 'javascript/ajax/scriptaculous.js';
    $scripts[] = 'javascript/ajax/behaviour.js';
    $scripts[] = 'javascript/ajax/lightbox.js';
    $scripts[] = 'javascript/overlib/overlib.js';
	foreach ($scripts as $script) Pageutil::addvar('javascript',$script);
	PageUtil::addVar('stylesheet', 'javascript/ajax/lightbox/lightbox.css');

	// Now the main needle part    
    if(!empty($nid)) {
       if(pnModAvailable('UserPictures')) {
			// Get picture
            $pictures = pnModAPIFunc('UserPictures','user','get',array(
				'id'  => $nid,
				'key' => $key
		      ));
            if ($pictures[0]['id'] > 0) $code = (string)$pictures[0]['code_thumbnail'];
		    if (isset($code)) 	{
		      	if (pnModGetName() == 'pnWikka') return '{{image url="'.pngetBaseURL().$pictures[0]['filename_absolute'].'.thumb.jpg" link="'.pngetBaseURL().$pictures[0]['url'].'"}}';
				else return $code;
			}
            else 				return '<em>' . DataUtil::formatForDisplay(_USERPICTURESNOTFOUNDORNOPERMISSION) . '</em>';
        } 
        else return '<em>' . DataUtil::formatForDisplay(_USERPICTURESNOTAVAILABLE) . '</em>';
    } 
	else return '<em>' . DataUtil::formatForDisplay(_USERPICTURESNONEEDLEID) . '</em>';
}
?>