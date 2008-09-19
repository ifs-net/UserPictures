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
 * Return an array of items to show in the your account panel
 *
 * @return   array   
 */
function UserPictures_accountapi_getall($args)
{
    // Create an array of links to return
    pnModLangLoad('UserPictures');
    $items = array(
					array(	'url'     	=> pnModURL('UserPictures', 'user','main'),
        					'title'   	=> _USERPICTURESOWNPICTURES,
                        	'icon'		=> 'userpictures.gif')
							 );
    // Return the items
    return $items;
}
?>