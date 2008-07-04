<?php
/**
 * Populate pntables array for UserPictures module
 *
 * This function is called internally by the core whenever the module is
 * loaded. It delivers the table information to the core.
 * It can be loaded explicitly using the pnModDBInfoLoad() API function.
 *
 * @author       Jim McDonald
 * @version      $Revision: 14662 $
 * @return       array       The table information.
 */
function UserPictures_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the UserPictures item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $UserPictures = pnConfigGetVar('prefix') . '_userpictures';
    $UserPictures_templates = pnConfigGetVar('prefix') . '_userpictures_templates';
    $UserPictures_persons = pnConfigGetVar('prefix') . '_userpictures_persons';
    $UserPictures_settings = pnConfigGetVar('prefix') . '_userpictures_settings';
    $UserPictures_categories = pnConfigGetVar('prefix') . '_userpictures_categories';
    $UserPictures_catassoc = pnConfigGetVar('prefix') . '_userpictures_catassoc';

    // Set the table name
    $pntable['userpictures'] = $UserPictures;
    $pntable['userpictures_templates'] = $UserPictures_templates;
    $pntable['userpictures_persons'] = $UserPictures_persons;
    $pntable['userpictures_settings'] = $UserPictures_settings;
    $pntable['userpictures_categories'] = $UserPictures_categories;
    $pntable['userpictures_catassoc'] = $UserPictures_catassoc;

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['userpictures_column'] = array(
					'id'      	=> 'pn_id',
                                        'uid'	 	=> 'pn_uid',
					'template_id'	=> 'pn_template_id',
					'comment'	=> 'pn_comment',
					'filename'	=> 'pn_filename',
                                        'verified'  	=> 'pn_verified'
				        );
				       
    $pntable['userpictures_templates_column'] = array(
					'id'		=> 'pn_id',
					'title'		=> 'pn_title',
					'max_width'	=> 'pn_max_width',
					'max_height'	=> 'pn_max_height',
					'defaultimage'	=> 'pn_defaultimage',
					'to_verify'	=> 'pn_to_verify'
					);

    $pntable['userpictures_persons_column'] = array(
					'id'		=> 'pn_id',
					'picture_id'	=> 'pn_picture_id',
					'uid'		=> 'pn_uid'
					);

    $pntable['userpictures_settings_column'] = array(
					'uid'		=> 'pn_uid',
					'nolinking'	=> 'pn_nolinking',
					'nocomments'	=> 'pn_nocomments',
					'picspublic'	=> 'pn_picspublic'
					);

    $pntable['userpictures_catassoc_column'] = array(
					'id'		=> 'pn_id',
					'picture_id'	=> 'pn_picture_id',
					'cat_id'	=> 'pn_cat_id',
					'uid'		=> 'pn_uid'
					);

    $pntable['userpictures_categories_column'] = array(
					'id'		=> 'pn_id',
					'uid'		=> 'pn_uid',
					'title'		=> 'pn_title',
					'text'		=> 'pn_text',
					'sortnr'	=> 'pn_uid'
					);

    // Return the table information
    return $pntable;
}

?>