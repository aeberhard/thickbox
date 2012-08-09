<?php
/**
 * --------------------------------------------------------------------
 *
 * Redaxo Addon: Thickbox
 * Version: 1.6, 19.03.2008
 *
 * Autor: Andreas Eberhard, andreas.eberhard@gmail.com
 *        http://rex.andreaseberhard.de
 * 
 * Verwendet wird das Script von Cody Lindley
 * http://jquery.com/demo/thickbox/
 *
 * --------------------------------------------------------------------
 */

	// Include config
	include dirname(__FILE__).'/config.inc.php';

	// Include Header and Navigation
	include $REX['INCLUDE_PATH'].'/layout/top.php';

	// Addon-Subnavigation
	$subpages = array(
		array('',$rxa_thickbox['i18n']->msg('menu_settings')),
		array('info',$rxa_thickbox['i18n']->msg('menu_information')),
		array('log',$rxa_thickbox['i18n']->msg('menu_changelog')),
		array('mod',$rxa_thickbox['i18n']->msg('menu_modules')),
	);

	// Titel
	if ( in_array($rxa_thickbox['rexversion'], array('3.11')) ) {
		title($rxa_thickbox['i18n']->msg('title'), $subpages);
	} else {
		rex_title($rxa_thickbox['i18n']->msg('title'), $subpages);
	}

	// Include der angeforderten Seite
	if (isset($_GET['subpage'])) {
		$subpage = $_GET['subpage'];
	} else {
		$subpage = '';
	}
	switch($subpage) {
		case 'info':
			include ($rxa_thickbox['path'] .'/pages/help.inc.php');
		break;
		case 'log':
			include ($rxa_thickbox['path'] .'/pages/changelog.inc.php');
		break;
		case 'mod':
			include ($rxa_thickbox['path'] .'/pages/modules.inc.php');
		break;
		default:
			include ($rxa_thickbox['path'] .'/pages/default_page.inc.php');
		break;		
	}
 
	// Include Footer
	include $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>