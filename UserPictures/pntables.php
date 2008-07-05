<?php
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
    $pntable['userpictures'] 			= $UserPictures;
    $pntable['userpictures_templates'] 	= $UserPictures."_templates";
    $pntable['userpictures_persons'] 	= $UserPictures."_persons";
    $pntable['userpictures_settings'] 	= $UserPictures."_settings";
    $pntable['userpictures_categories']	= $UserPictures."_categories";
    $pntable['userpictures_catassoc'] 	= $UserPictures."_catassoc";

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['userpictures_column'] = array(
				'id'      				=> 'pn_id',
                'uid'	 				=> 'pn_uid',
                'position'				=> 'pn_position',
				'template_id'			=> 'pn_template_id',
				'comment'				=> 'pn_comment',
				'coords'				=> 'pn_coords',
				'filename'				=> 'pn_filename',
			    'verified'  			=> 'pn_verified'
			    );
    $pntable['userpictures_column_def'] = array(
    			'id'					=> "I	AUTOINCREMENT PRIMARY",
    			'uid'					=> "I	NOTNULL DEFAULT 0",
    			'position'				=> "I	NOTNULL DEFAULT 0",
    			'template_id'			=> "I	NOTNULL DEFAULT 0",
        		'comment'				=> "XL	NOTNULL DEFAULT ''",
        		'coords'				=> "XL	NOTNULL DEFAULT ''",
        		'filename'				=> "XL	NOTNULL DEFAULT ''",
        		'verified'				=> "C(1)	NOTNULL DEFAULT '0'"
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
				'uid'					=> 'pn_uid'
				);
    $pntable['userpictures_persons_column_def'] = array(
        		'id'					=> "I	AUTOINCREMENT PRIMARY",
    			'picture_id'			=> "I	NOTNULL DEFAULT 0",
        		'uid'					=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_settings_column'] = array(
				'uid'					=> 'pn_uid',
				'nolinking'				=> 'pn_nolinking',
				'nocomments'			=> 'pn_nocomments',
				'picspublic'			=> 'pn_picspublic'
				);
    $pntable['userpictures_settings_column_def'] = array(
		        'uid'					=> "I	AUTOINCREMENT PRIMARY",
		        'nolinking'				=> "I	NOTNULL DEFAULT 0",
		        'nocomments'			=> "I	NOTNULL DEFAULT 0",
		        'picspublic'			=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_catassoc_column'] = array(
				'id'					=> 'pn_id',
				'picture_id'			=> 'pn_picture_id',
				'cat_id'				=> 'pn_cat_id',
				'uid'					=> 'pn_uid'
				);
    $pntable['userpictures_catassoc_column_def'] = array(
		        'id'					=> "I	AUTOINCREMENT PRIMARY",
		        'picture_id'			=> "I	NOTNULL DEFAULT 0",
		        'cat_id'				=> "I	NOTNULL DEFAULT 0",
		        'uid'					=> "I	NOTNULL DEFAULT 0"
    			);
    $pntable['userpictures_categories_column'] = array(
				'id'					=> 'pn_id',
				'uid'					=> 'pn_uid',
				'title'					=> 'pn_title',
				'text'					=> 'pn_text',
				'sortnr'				=> 'pn_uid'
				);
    $pntable['userpictures_categories_column_def'] = array(
		        'id'					=> "I	AUTOINCREMENT PRIMARY",
		        'uid'					=> "I	NOTNULL DEFAULT 0",
		        'title'					=> "XL	NOTNULL DEFAULT ''",
		        'text'					=> "XL	NOTNULL DEFAULT ''",
		        'sortnr'				=> "I	NOTNULL DEFAULT 0"
    			);
    // Return the table information
    return $pntable;
}
?>