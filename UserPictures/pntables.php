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
 * Populate pntables array for UserPictures module
 *
 * @return array
 */
function UserPictures_pntables()
{
    // Initialise table array
    $pntable = array();

    $UserPictures = pnConfigGetVar('prefix') . '_userpictures';

    // Set the table name
    $pntable['userpictures'] 					= $UserPictures;
    $pntable['userpictures_templates'] 			= $UserPictures."_templates";
    $pntable['userpictures_persons'] 			= $UserPictures."_persons";
    $pntable['userpictures_settings'] 			= $UserPictures."_settings";
    $pntable['userpictures_categories']			= $UserPictures."_categories";
    $pntable['userpictures_catassoc'] 			= $UserPictures."_catassoc";
    $pntable['userpictures_globalcategories']	= $UserPictures."_globalcategories";
    $pntable['userpictures_globalcatassoc'] 	= $UserPictures."_globalcatassoc";

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['userpictures_column'] = array(
				'id'      				=> 'pn_id',
                'uid'	 				=> 'pn_uid',
                'position'				=> 'pn_position',
				'template_id'			=> 'pn_template_id',
				'comment'				=> 'pn_comment',
				'filename'				=> 'pn_filename',
			    'verified'  			=> 'pn_verified',
			    'date'		  			=> 'date',
			    'global_category'		=> 'global_category',
			    'category'				=> 'category',
			    'coord_lat'				=> 'coord_lat',
			    'coord_lng'				=> 'coord_lng',
			    'privacy_status'		=> 'privacy_status'
			    );
    $pntable['userpictures_column_def'] = array(
    			'id'					=> "I	AUTOINCREMENT PRIMARY",
    			'uid'					=> "I	NOTNULL DEFAULT 0",
    			'position'				=> "I	NOTNULL DEFAULT 0",
    			'template_id'			=> "I	NOTNULL DEFAULT 0",
        		'comment'				=> "XL	NOTNULL DEFAULT ''",
        		'filename'				=> "XL	NOTNULL DEFAULT ''",
        		'verified'				=> "C(1)	NOTNULL DEFAULT '0'",
        		'date'					=> "T	NOTNULL DEFAULT '0000-00-00 00:00:00'",
        		'global_category'		=> "I	NOTNULL DEFAULT 0",
        		'category'				=> "I	NOTNULL DEFAULT 0",
        		'coord_lat'				=> "F	NOTNULL DEFAULT 0",
        		'coord_lng'				=> "F	NOTNULL DEFAULT 0",
        		'privacy_status'		=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_templates_column'] = array (
				'id'					=> 'pn_id',
				'title'					=> 'pn_title',
				'max_width'				=> 'pn_max_width',
				'max_height'			=> 'pn_max_height',
				'defaultimage'			=> 'pn_defaultimage',
				'to_verify'				=> 'pn_to_verify'
			    );
	$pntable['userpictures_templates_column_def'] = array (
        		'id'					=> "I	AUTOINCREMENT PRIMARY",
        		'title'					=> "XL	NOTNULL DEFAULT ''",
        		'max_width'				=> "I	NOTNULL DEFAULT 320",
        		'max_height'			=> "I	NOTNULL DEFAULT 200",
        		'defaultimage'			=> "XL	NOTNULL DEFAULT ''",
        		'to_verify'				=> "C(1)	NOTNULL DEFAULT '0'"
				);
	$pntable['userpictures_persons_column'] = array(
				'id'					=> 'pn_id',
				'picture_id'			=> 'pn_picture_id',
				'uid'					=> 'pn_uid',
				'assoc_uid'				=> 'assoc_uid'
				);
    $pntable['userpictures_persons_column_def'] = array(
        		'id'					=> "I	AUTOINCREMENT PRIMARY",
    			'picture_id'			=> "I	NOTNULL DEFAULT 0",
        		'uid'					=> "I	NOTNULL DEFAULT 0",
        		'assoc_uid'				=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_settings_column'] = array(
				'uid'					=> 'pn_uid',
				'nolinking'				=> 'pn_nolinking',
				'nocomments'			=> 'pn_nocomments',
				'picspublic'			=> 'pn_picspublic'
				);
    $pntable['userpictures_settings_column_def'] = array(
		        'uid'					=> "I	NOTNULL DEFAULT 0",
		        'nolinking'				=> "I	NOTNULL DEFAULT 0",
		        'nocomments'			=> "I	NOTNULL DEFAULT 0",
		        'picspublic'			=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_categories_column'] = array(
				'id'					=> 'pn_id',
				'uid'					=> 'pn_uid',
				'title_image'			=> 'title_img',
				'title'					=> 'pn_title',
				'text'					=> 'pn_text'
				);
    $pntable['userpictures_categories_column_def'] = array(
		        'id'					=> "I	AUTOINCREMENT PRIMARY",
		        'uid'					=> "I	NOTNULL DEFAULT 0",
		        'title_image'			=> "I	NOTNULL DEFAULT 0",
		        'title'					=> "XL	NOTNULL DEFAULT ''",
		        'text'					=> "XL	NOTNULL DEFAULT ''"
    			);
    $pntable['userpictures_globalcategories_column'] = array(
				'id'					=> 'id',
				'title'					=> 'title',
				'text'					=> 'text',
				'date'					=> 'date'
				);
    $pntable['userpictures_globalcategories_column_def'] = array(
		        'id'					=> "I	AUTOINCREMENT PRIMARY",
		        'title'					=> "XL	NOTNULL DEFAULT ''",
		        'text'					=> "XL	NOTNULL DEFAULT ''",
		        'date'					=> "D	NOTNULL DEFAULT '0000-00-00'"
    			);
    // Return the table information
    return $pntable;
}
?>