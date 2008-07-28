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
 * initialise the UserPictures module
 *
 * @return       bool       true on success, false otherwise
 */
function UserPictures_init()
{
  	// Create Tables
  	if (!DBUtil::createTable('userpictures')) return false;
  	if (!DBUtil::createTable('userpictures_templates')) return false;
  	if (!DBUtil::createTable('userpictures_persons')) return false;
  	if (!DBUtil::createTable('userpictures_settings')) return false;
  	if (!DBUtil::createTable('userpictures_categories')) return false;
  	if (!DBUtil::createTable('userpictures_catassoc')) return false;
  	if (!DBUtil::createTable('userpictures_globalcategories')) return false;
  	if (!DBUtil::createTable('userpictures_globalcatassoc')) return false;


    // If the interactive installation has run, we hve some values now in sessionvars
    // that we must use
    $activated = pnSessionGetVar('userpictures_activated');
    pnModSetVar('UserPictures', 'activated', (($activated<>false) ? $activated : 0));
    $maxwidth = pnSessionGetVar('userpictures_maxwidth');
    pnModSetVar('UserPictures', 'maxwidth', (($maxwidth<>false) ? $maxwidth : 800));
    $maxheight = pnSessionGetVar('userpictures_maxheight');
    pnModSetVar('UserPictures', 'maxheight', (($maxheight<>false) ? $maxheight : 600));
    $ownuploads = pnSessionGetVar('userpictures_ownuploads');
    pnModSetVar('UserPictures', 'ownuploads', (($ownuploads<>false) ? $ownuploads : 0));
    $maxfilesize = pnSessionGetVar('userpictures_maxfilesize');
    pnModSetVar('UserPictures', 'maxfilesize', (($maxfilesize<>false) ? $maxfilesize : 1500));
    $verifytext = pnSessionGetVar('userpictures_verifytext');
    pnModSetVar('UserPictures', 'verifytext', (($verifytext<>false) ? $verifytext : ''));
    $convert = pnSessionGetVar('userpictures_convert');
    pnModSetVar('UserPictures', 'convert', (($convert<>false) ? $convert : '/usr/bin/convert'));
    $thumbnailsize = pnSessionGetVar('userpictures_thumbnailsize');
    pnModSetVar('UserPictures', 'thumbnailsize', (($thumbnailsize<>false) ? $thumbnailsize : '110x110'));
    $disabledtext = pnSessionGetVar('userpictures_disabledtext');
    pnModSetVar('UserPictures', 'disabledtext', (($disabledtext<>false) ? $disabledtext : ''));
    $datadir = pnSessionGetVar('userpictures_datadir');
    pnModSetVar('UserPictures', 'datadir', (($datadir<>false) ? $datadir : 'modules/UserPictures/data/'));
    $thumbnailcreation = pnSessionGetVar('userpictures_thumbnailcreation');
    pnModSetVar('UserPictures', 'thumbnailcreation', (($thumbnailcreation<>false) ? $thumbnailcreation : 'gdlib'));
    $hint = pnSessionGetVar('userpictures_hint');
    pnModSetVar('UserPictures', 'hint', (($hint<>false) ? $hint : 1));
    $avatardir = pnSessionGetVar('userpictures_avatardir');
    pnModSetVar('UserPictures', 'avatardir', (($avatardir<>false) ? $avatardir : 'images/avatar'));

    // clean up
    pnSessionDelVar('userpictures_hint');
    pnSessionDelVar('userpictures_thumbnailcreation');
    pnSessionDelVar('userpictures_activated');
    pnSessionDelVar('userpictures_thumbnailsize');
    pnSessionDelVar('userpictures_convert');
    pnSessionDelVar('userpictures_maxfilesize');
    pnSessionDelVar('userpictures_maxwidth');
    pnSessionDelVar('userpictures_maxheight');
    pnSessionDelVar('userpictures_verifytext');
    pnSessionDelVar('userpictures_disabledtext');
    pnSessionDelVar('userpictures_datadir');
    pnSessionDelVar('userpictures_avatardir');

    // Initialisation successful
    return true;
}

/**
 * upgrade the UserPictures module
 *
 * @return       bool       true on success, false otherwise
 */
function UserPictures_upgrade($oldversion)
{
    switch($oldversion) {
        case '0.20':
	    // we have to add some new module variables
	    pnModSetVar('UserPictures', 'thumbnailsize','110x110');
	    pnSessionDelVar('userpictures_thumbnailsize');
		case '0.40':
		    $dbconn  =& pnDBGetConn(true);
	    	    $pntable =& pnDBGetTables();
	
	            // It's good practice to name the table and column definitions you
		    // are getting - $table and $column don't cut it in more complex
		    // modules
		    $UserPictures_personstable  = &$pntable['userpictures_persons'];
		    $UserPictures_personscolumn = &$pntable['userpictures_persons_column'];
	
		    $UserPictures_categoriestable  = &$pntable['userpictures_categories'];
		    $UserPictures_categoriescolumn = &$pntable['userpictures_categories_column'];
	
		    $UserPictures_catassoctable  = &$pntable['userpictures_catassoc'];
		    $UserPictures_catassoccolumn = &$pntable['userpictures_catassoc_column'];
	
		    // Create the table
		    $dict = &NewDataDictionary($dbconn);
	
		    // Define array containing specific table options
		    // This variable only need populating once as the same table options will
		    // apply for all tables to be created.
		    $taboptarray =& pnDBGetTableOptions();
	
		    // Define the fields in the form:
		    // $fieldname $type $colsize $otheroptions
		    $flds_persons = "
		        $UserPictures_personscolumn[id]			I	AUTOINCREMENT PRIMARY,
		        $UserPictures_personscolumn[picture_id]		I	NOTNULL DEFAULT 0,
		        $UserPictures_personscolumn[uid]		I	NOTNULL DEFAULT 0
			        ";
	
		    $flds_catassoc = "
		        $UserPictures_catassoccolumn[id]			I	AUTOINCREMENT PRIMARY,
		        $UserPictures_catassoccolumn[picture_id]		I	NOTNULL DEFAULT 0,
		        $UserPictures_catassoccolumn[cat_id]			I	NOTNULL DEFAULT 0,
		        $UserPictures_catassoccolumn[uid]			I	NOTNULL DEFAULT 0
			        ";
	
		    $flds_categories = "
		        $UserPictures_categoriescolumn[id]			I	AUTOINCREMENT PRIMARY,
		        $UserPictures_categoriescolumn[uid]			I	NOTNULL DEFAULT 0,
		        $UserPictures_categoriescolumn[title]			XL	NOTNULL DEFAULT '',
		        $UserPictures_categoriescolumn[text]			XL	NOTNULL DEFAULT '',
		        $UserPictures_categoriescolumn[sortnr]			I	NOTNULL DEFAULT 0
			        ";
	
		    // Creating the table for the templates
		    $sqlarray = $dict->CreateTableSQL($UserPictures_personstable, $flds_persons, $taboptarray);
	
		    // Check for an error with the database code, and if so set an
		    // appropriate error message and return
		    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
		        pnSessionSetVar('errormsg', _USERPICTURESCREATEPERSONSTABLEFAILED."-".$sqlarray[0]);
		        return false;
		    }
			
	
		    $sqlarray = $dict->CreateTableSQL($UserPictures_catassoctable, $flds_catassoc, $taboptarray);
		    // Check for an error with the database code, and if so set an
		    // appropriate error message and return
		    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
		        pnSessionSetVar('errormsg', _USERPICTURESCREATECATASSOCTABLEFAILED."-".$sqlarray[0]);
		        return false;
		    }
		    $sqlarray = $dict->CreateTableSQL($UserPictures_categoriestable, $flds_categories, $taboptarray);
		    // Check for an error with the database code, and if so set an
		    // appropriate error message and return
		    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
		        pnSessionSetVar('errormsg', _USERPICTURESCREATECATEGORIESTABLEFAILED."-".$sqlarray[0]);
		        return false;
		    }
		case '0.51':
		    // we'll introduce a new module variable
		    // the data-directory's path will be customizable
		    if (!pnModSetVar('UserPictures','datadir','modules/UserPictures/data/')) return false;
		case '0.60':
		    // we'll introduce new module variables
		    if (!pnModSetVar('UserPictures','avatarmanagement','0')) return false;
		    if (!pnModSetVar('UserPictures','avatarsize','100x100')) return false;
		case '0.70':
		    $dbconn  =& pnDBGetConn(true);
	    	    $pntable =& pnDBGetTables();
	
	            // It's good practice to name the table and column definitions you
		    // are getting - $table and $column don't cut it in more complex
		    // modules
		    $UserPictures_settingstable  = &$pntable['userpictures_settings'];
		    $UserPictures_settingscolumn = &$pntable['userpictures_settings_column'];
	
		    // Create the table
		    $dict = &NewDataDictionary($dbconn);
	
		    // Define array containing specific table options
		    // This variable only need populating once as the same table options will
		    // apply for all tables to be created.
		    $taboptarray =& pnDBGetTableOptions();
	
		    // Define the fields in the form:
		    // $fieldname $type $colsize $otheroptions
		    $flds_settings = "
		        $UserPictures_settingscolumn[uid]			I	AUTOINCREMENT PRIMARY,
		        $UserPictures_settingscolumn[nolinking]			I	NOTNULL DEFAULT 0,
		        $UserPictures_settingscolumn[picspublic]		I	NOTNULL DEFAULT 0
			        ";
	
		    // Creating the table for the templates
		    $sqlarray = $dict->CreateTableSQL($UserPictures_settingstable, $flds_settings, $taboptarray);
	
		    // Check for an error with the database code, and if so set an
		    // appropriate error message and return
		    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
		        pnSessionSetVar('errormsg', _USERPICTURESCREATESETTINGSTABLEFAILED."-".$sqlarray[0]);
		        return false;
		    }
	
		    $thumbnailcreation = pnSessionGetVar('userpictures_thumbnailcreation');
		    pnModSetVar('UserPictures', 'thumbnailcreation', (($thumbnailcreation<>false) ? $thumbnailcreation : 'gdlib'));
		    $hint = pnSessionGetVar('userpictures_hint');
		    pnModSetVar('UserPictures', 'hint', (($hint<>false) ? $hint : 1));
		    // clean up
		    pnSessionDelVar('userpictures_hint');
		    pnSessionDelVar('userpictures_thumbnailcreation');
		case '0.80':
		    $dbconn  =& pnDBGetConn(true);
	    	    $pntable =& pnDBGetTables();
		    $UserPictures_settingstable  = &$pntable['userpictures_settings'];
		    $UserPictures_settingscolumn = &$pntable['userpictures_settings_column'];
	
		    $dict = &NewDataDictionary($dbconn);
	
		    $taboptarray =& pnDBGetTableOptions();
	
		    $flds_settings = "
		        $UserPictures_settingscolumn[nocomments]			I	NOTNULL DEFAULT 0
			        ";
	
		    $sqlarray = $dict->AddColumnSQL($UserPictures_settingstable,$flds_settings);
		    if ($dict->ExecuteSQLArray($sqlarray) != 2) {
			pnSessionSetVar('errormsg', 'Error altering Table'.$sqlarray[0]);
		        return false;
	    	    }
		case '0.90':
		case '0.91':
		case '0.92':
		// all for 1.0 now!
		case '0.93':
			if (!DBUtil::changeTable('userpictures')) return false;
			if (!DBUtil::changeTable('userpictures_globalcategories')) return false;
			if (!DBUtil::changeTable('userpictures_globalcatassoc')) return false;
		default:
		    return true;
	}
}

/**
 * delete the UserPictures module
 *
 * @return       bool       true on success, false otherwise
 */
function UserPictures_delete()
{
  	if (!DBUtil::dropTable('userpictures')) return false;
  	if (!DBUtil::dropTable('userpictures_templates')) return false;
  	if (!DBUtil::dropTable('userpictures_persons')) return false;
  	if (!DBUtil::dropTable('userpictures_settings')) return false;
  	if (!DBUtil::dropTable('userpictures_categories')) return false;
  	if (!DBUtil::dropTable('userpictures_catassoc')) return false;
  	if (!DBUtil::dropTable('userpictures_globalcategories')) return false;
  	if (!DBUtil::dropTable('userpictures_globalcatassoc')) return false;

    // Delete any module variables
    pnModDelVar('UserPictures');

    // Deletion successful
    return true;
}
?>