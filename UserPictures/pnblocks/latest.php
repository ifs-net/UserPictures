<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schie�l
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * initialise block
 * 
 * @author       The PostNuke Development Team
 */
function UserPictures_latestblock_init()
{
    // Security
    pnSecAddSchema('UserPictures:latestblock:', 'Block title::');
}

/**
 * get information on block
 * 
 * @author       The PostNuke Development Team
 * @return       array       The block information
 */
function UserPictures_latestblock_info()
{
    return array('text_type'      => 'latest',
                 'module'         => 'UserPictures',
                 'text_type_long' => 'Show latest UserPictures items',
                 'allow_multiple' => true,
                 'form_content'   => false,
                 'form_refresh'   => false,
                 'show_preview'   => true);
}

/**
 * display block
 * 
 * @author       The PostNuke Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the rendered bock
 */
function UserPictures_latestblock_display($blockinfo)
{
    if (!pnSecAuthAction(0,
                         'UserPictures:latestblock:',
                         "$blockinfo[title]::",
                         ACCESS_READ)) {
        return false;
    }

    // Get variables from content block
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // Defaults
    if (empty($vars['numitemscol'])) {
        $vars['numitemscol'] = 2;
    }
    

    // Check if the UserPictures module is available. 
    if (!pnModAvailable('UserPictures')) {
    	return false;
    }

    // Create output object
    $pnRender =& new pnRender('UserPictures');
    
    // some cache settings
    $pnRender->caching = true;
    $pnRender->cache_lifetime = 180;
	    
    $amount = $vars['numitemscol']*$vars['numitems'];
    $pnRender->assign('amount',$amount);
    
    for ($i=1;$i<$vars['numitems'];$i++) $cyclevalue.=",";
    $cyclevalue.="</tr><tr>";
    
    $pnRender->assign('cyclevalue',$cyclevalue);
    
    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('userpictures_block_latest.htm');
    return themesideblock($blockinfo);
}


/**
 * modify block settings
 * 
 * @author       The PostNuke Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function UserPictures_latestblock_modify($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // Defaults
    if (empty($vars['numitemscol'])) {
        $vars['numitemscol'] = 2;
    }

    // Create output object
	$pnRender =& new pnRender('UserPictures');

	// As Admin output changes often, we do not want caching.
	$pnRender->caching = false;

    // assign the approriate values
	$pnRender->assign('numitems', $vars['numitems']);

    // assign the approriate values
	$pnRender->assign('numitemscol', $vars['numitemscol']);

    // Return the output that has been generated by this function
	return $pnRender->fetch('userpictures_block_latest_modify.htm');
}


/**
 * update block settings
 * 
 * @author       The PostNuke Development Team
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function UserPictures_latestblock_update($blockinfo)
{
    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);
	
	// alter the corresponding variable
    $vars['numitems'] = pnVarCleanFromInput('numitems');
	// alter the corresponding variable
    $vars['numitemscol'] = pnVarCleanFromInput('numitemscol');
	
	// write back the new contents
    $blockinfo['content'] = pnBlockVarsToContent($vars);

	// clear the block cache
	$pnRender =& new pnRender('UserPictures');
	$pnRender->clear_cache('UserPictures_block_latest.htm');
	
    return $blockinfo;
}

?>