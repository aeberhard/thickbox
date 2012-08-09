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
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 0;
		return;
	}

   // Gültige REDAXO-Version abfragen
	if ( !in_array($rxa_thickbox['rexversion'], array('3.11', '32', '40', '41', '42')) ) {
		echo '<font color="#cc0000"><strong>Fehler! Ung&uuml;ltige REDAXO-Version - '.$rxa_thickbox['rexversion'].'</strong></font>';
		$REX['ADDON']['installmsg'][$rxa_thickbox['name']] = '<br /><br /><font color="#cc0000"><strong>Fehler! Ung&uuml;ltige REDAXO-Version - '.$rxa_thickbox['rexversion'].'</strong></font>';
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 0;
		return;
	}

	// Verzeichnis files/thickbox anlegen
	if ( !@is_dir($rxa_thickbox['filesdir']) ) {
		if ( !@mkdir($rxa_thickbox['filesdir']) ) {
			$rxa_thickbox['meldung'] .= $rxa_thickbox['i18n']->msg('error_createdir', $rxa_thickbox['filesdir']);
		}
	}

	// Dateien ins Verzeichnis files/thickbox kopieren
	if ($dh = opendir($rxa_thickbox['sourcedir'])) {
		while ($el = readdir($dh)) {
			$rxa_thickbox['file'] = $rxa_thickbox['sourcedir'].'/'.$el;
			if ($el != '.' && $el != '..' && is_file($rxa_thickbox['file'])) {
				if ( !@copy($rxa_thickbox['file'], $rxa_thickbox['filesdir'].'/'.$el) ) {
					$rxa_thickbox['meldung'] .= $rxa_thickbox['i18n']->msg('error_copyfile', $el, $REX['HTDOCS_PATH'].'files/'.$rxa_thickbox['name'].'/');
				}
			}
		}
	} else {
		$rxa_thickbox['meldung'] .= $rxa_thickbox['i18n']->msg('error_readdir',$rxa_thickbox['sourcedir']);
	}
	
	// Evtl Ausgabe einer Meldung
	// $rxa_thickbox['meldung'] = 'Das Addon wurde nicht installiert, weil...';
	if ( $rxa_thickbox['meldung']<>'' ) {
		$REX['ADDON']['installmsg'][$rxa_thickbox['name']] = '<br /><br />'.$rxa_thickbox['meldung'].'<br /><br />';
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 0;
	} else {
	// Installation erfolgreich
		$REX['ADDON']['install'][$rxa_thickbox['name']] = 1;
	}
?>