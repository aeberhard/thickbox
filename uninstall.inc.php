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

	unset($rxa_thickbox); 
	include('config.inc.php');
	
	if (!isset($rxa_thickbox['name'])) {
		echo '<font color="#cc0000"><strong>Fehler! Eventuell wurde die Datei config.inc.php nicht gefunden!</strong></font>';
		return;
	}

	// Dateien aus dem Ordner files/thickbox löschen
	if (isset($rxa_thickbox['filesdir']) and ($rxa_thickbox['filesdir']<>'') and ($rxa_thickbox['name']<>'') ) {
		if ($dh = opendir($rxa_thickbox['filesdir'])) {
			while ($el = readdir($dh)) {
				$path = $rxa_thickbox['filesdir'].'/'.$el;
				if ($el != '.' && $el != '..' && is_file($path)) {
					@unlink($path);
				}
			}
		}
	}
	@closedir($dh);
	@rmdir($rxa_thickbox['filesdir']);	
	
	// Evtl Ausgabe einer Meldung
	// De-Installation nicht erfolgreich
	if ( $rxa_thickbox['meldung']<>'' ) {
		$REX['ADDON']['installmsg'][$rxa_thickbox['name']] = '<br /><br />'.$rxa_thickbox['meldung'].'<br /><br />';
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 1;
	// De-Installation erfolgreich
	} else {
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 0;
	}
?>