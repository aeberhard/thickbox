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

	// Name des Addons und Pfade
	unset($rxa_thickbox);
	$rxa_thickbox['name'] = 'thickbox';

	$REX['ADDON']['version'][$rxa_thickbox['name']] = '1.6';
	$REX['ADDON']['author'][$rxa_thickbox['name']] = 'Andreas Eberhard';

	$rxa_thickbox['path'] = $REX['INCLUDE_PATH'].'/addons/'.$rxa_thickbox['name'];
	$rxa_thickbox['basedir'] = dirname(__FILE__);
	$rxa_thickbox['lang_path'] = $REX['INCLUDE_PATH']. '/addons/'. $rxa_thickbox['name'] .'/lang';
	$rxa_thickbox['sourcedir'] = $REX['INCLUDE_PATH']. '/addons/'. $rxa_thickbox['name'] .'/'. $rxa_thickbox['name'];
	$rxa_thickbox['filesdir'] = $REX['HTDOCS_PATH'].'files/'.$rxa_thickbox['name'];
	$rxa_thickbox['meldung'] = '';
	$rxa_thickbox['rexversion'] = isset($REX['VERSION']) ? $REX['VERSION'] . $REX['SUBVERSION'] : $REX['version'] . $REX['subversion'];

/*
 * --------------------------------------------------------------------
 * Nur im Backend
 * --------------------------------------------------------------------
 */
	if (!$REX['GG']) {
		// Sprachobjekt anlegen
		$rxa_thickbox['i18n'] = new i18n($REX['LANG'],$rxa_thickbox['lang_path']);

		// Anlegen eines Navigationspunktes im REDAXO Hauptmenu
		$REX['ADDON']['page'][$rxa_thickbox['name']] = $rxa_thickbox['name'];
		// Namensgebung für den Navigationspunkt
		$REX['ADDON']['name'][$rxa_thickbox['name']] = $rxa_thickbox['i18n']->msg('menu_link');

		// Berechtigung für das Addon
		$REX['ADDON']['perm'][$rxa_thickbox['name']] = $rxa_thickbox['name'].'[]';
		// Berechtigung in die Benutzerverwaltung einfügen
		$REX['PERM'][] = $rxa_thickbox['name'].'[]';		
	}

/*
 * --------------------------------------------------------------------
 * Outputfilter für das Frontend
 * --------------------------------------------------------------------
 */
	if ($REX['GG'])
	{
		rex_register_extension('OUTPUT_FILTER', 'thickbox_opf');

		// Prüfen ob die aktuelle Kategorie mit der Auswahl übereinstimmt
		function thickbox_check_cat($acat, $aart, $subcats, $thickbox_cats)
		{

			// prüfen ob Kategorien ausgewählt
			if (!is_array($thickbox_cats)) return false;

			// aktuelle Kategorie in den ausgewählten dabei?
			if (in_array($acat, $thickbox_cats)) return true;

			// Prüfen ob Parent der aktuellen Kategorie ausgewählt wurde
			if ( ($acat > 0) and ($subcats == 1) )
			{
				$cat = OOCategory::getCategoryById($acat);
				while($cat = $cat->getParent())
				{
					if (in_array($cat->_id, $thickbox_cats)) return true;
				}
			}

			// evtl. noch Root-Artikel prüfen
			if (strstr(implode('',$thickbox_cats), 'r'))
			{
				if (in_array($aart.'r', $thickbox_cats)) return true;
			}

			// ansonsten keine Ausgabe!
			return false;
		}

      // Output-Filter
		function thickbox_opf($params)
		{
			global $REX, $REX_ARTICLE;
			global $rxa_thickbox;

			$content = $params['subject'];
			
			if ( !strstr($content,'</head>') or !file_exists($rxa_thickbox['path'].'/'.$rxa_thickbox['name'].'.ini')
			 or ( strstr($content,'<script type="text/javascript" src="files/thickbox/thickbox.js"></script>') and strstr($content,'<link rel="stylesheet" href="files/thickbox/thickbox.css" type="text/css" media="screen" />') ) ) {
				return $content;
			}

   		// Einstellungen aus ini-Datei laden
			if (($lines = file($rxa_thickbox['path'].'/'.$rxa_thickbox['name'].'.ini')) === FALSE) {
				return $content;
			} else {
				$va = explode(',', trim($lines[0]));
				$allcats = trim($va[0]);
				$subcats = trim($va[1]);
				$thickbox_cats = array();
				$thickbox_cats = unserialize(trim($lines[1]));
			}

			// aktuellen Artikel ermitteln
			$artid = isset($_GET['article_id']) ? $_GET['article_id']+0 : 0;
			if ($artid==0) {
				$artid = $REX_ARTICLE->getValue('article_id')+0;
			}
			if ($artid==0) { $artid = $REX['START_ARTICLE_ID']; }

			if (!$artid) { return $content; }

			$article = OOArticle::getArticleById($artid);
			if (!$article) { return $content; }

			// aktuelle Kategorie ermitteln
			if ( in_array($rxa_thickbox['rexversion'], array('3.11')) ) {
				$acat = $article->getCategoryId();
			}
			if ( in_array($rxa_thickbox['rexversion'], array('32', '40', '41', '42')) ) {
				$cat = $article->getCategory();
				if ($cat) {
					$acat = $cat->getId();
				}
			}
			// Wenn keine Kategorie ermittelt wurde auf -1 setzen für Prüfung in thickbox_check_cat, Prüfung auf Artikel im Root
			if (!$acat) { $acat = -1; }

         // Array anlegen falls keine Kategorien ausgewählt wurden
			if (!is_array($thickbox_cats)){
				$thickbox_cats = array();
			}

			// Code für Thickbox im head-Bereich ausgeben
			if ( ($allcats==1) or (thickbox_check_cat($acat, $artid, $subcats, $thickbox_cats) == true) )
			{
				$rxa_thickbox['output'] = '	<!-- Addon Thickbox '.$REX['ADDON']['version'][$rxa_thickbox['name']].' -->'."\n";
				$rxa_thickbox['output'] .= '	<script type="text/javascript" src="files/thickbox/jquery-latest.pack.js"></script>'."\n";
				$rxa_thickbox['output'] .= '	<script type="text/javascript" src="files/thickbox/thickbox-compressed.js"></script>'."\n";
				$rxa_thickbox['output'] .= '	<link rel="stylesheet" href="files/thickbox/thickbox.css" type="text/css" media="screen" />'."\n";
				$content = str_replace('</head>', $rxa_thickbox['output'].'</head>', $content);
			}

			return $content;
		}

	}
?>