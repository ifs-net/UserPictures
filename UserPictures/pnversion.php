<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

// The following information is used by the Modules module 
// for display and upgrade purposes
$modversion['name']           = 'UserPictures';
// the version string must not exceed 10 characters!
$modversion['version']        = '0.94';
$modversion['description']    = 'Give your users the chance to create own galleries and enhance your profile pages etc. with user pictures using UserPictures';
$modversion['displayname']    = 'UserPictures';

// The following in formation is used by the credits module
// to display the correct credits
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['credits']        = 'pndocs/credits.txt';
$modversion['help']           = 'pndocs/help.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Florian Schießl';
$modversion['contact']        = 'http://www.ifs-net.de/';

// The following information tells the PostNuke core that this
// module has an admin option.
$modversion['admin']          = 1;

// This one adds the info to the DB, so that users can click on the 
// headings in the permission module
$modversion['securityschema'] = array('UserPictures::' => 'UserPictures item name::UserPictures item ID');

// Some module dependencies
$modversion['dependencies'] = array(
	array(	'modname'    => 'EZComments',
			'minversion' => '1.6', 'maxversion' => '',
            'status'     => PNMODULE_DEPENDENCY_RECOMMENDED),
	array(	'modname'    => 'ContactList',
			'minversion' => '1.0', 'maxversion' => '',
            'status'     => PNMODULE_DEPENDENCY_RECOMMENDED),
    array(	'modname'    => 'MultiHook',
            'minversion' => '5.0', 'maxversion' => '',
            'status'     => PNMODULE_DEPENDENCY_RECOMMENDED)
	);

?>