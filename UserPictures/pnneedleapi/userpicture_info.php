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
 * UserPicture needle info
 * @param none
 * @return string with short usage description
 */
function UserPictures_needleapi_userpicture_info()
{
    $info = array('module'  => 'UserPictures', 
                  'info'    => 'USERPICTURE{pictureID}',
                  'inspect' => false);
    return $info;
}
