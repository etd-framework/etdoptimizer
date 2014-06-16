<?php
/**
 * @package		ETD Solutions
 * @subpackage	Plugin ETDOptimizer
 * 
 * @version		1.1.1
 * @copyright	Copyright (C) 2012 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license		http://www.etd-solutions.com/licence
 * @author		ETD Solutions http://www.etd-solutions.com
**/

require_once(JPATH_PLUGINS.'/system/etdoptimizer/part.php');

class EtdOptimizerHead extends EtdOptimizerPart {
	
	public function render() {
		
		$doc  = JFactory::getDocument();
		$template = JFactory::getApplication()->getTemplate();
		$template_path = JPATH_THEMES . '/' . $template;
		$template_uri = JUri::root(true) . '/templates/' . $template;
		ob_start();

		echo "<meta charset=\"" . $doc->getCharset() . "\">\n";
		echo "<title>" . htmlspecialchars($doc->title, ENT_COMPAT, $doc->getCharset()) . "</title>\n";

		if (!empty($doc->description)) {
			echo "<meta name=\"description\" content=\"" . htmlspecialchars($doc->description,
																			ENT_COMPAT,
																			$doc->getCharset()) . "\" />\n";
		}

		if (array_key_exists('keywords',
							 $doc->_metaTags['standard']) && !empty($doc->_metaTags['standard']['keywords'])
		) {
			echo "<meta name=\"keywords\" content=\"" . htmlspecialchars($doc->_metaTags['standard']['keywords'],
																		 ENT_COMPAT,
																		 $doc->getCharset()) . "\" />\n";
		}

        $viewport = $this->params->get('viewport');
        if (!empty($viewport)) {
            echo "<meta name=\"viewport\" content=\"" . $viewport . "\">\n";
        }

		if (!empty($doc->_links)) {
			foreach ($doc->_links as $link => $linkAtrr) {
				if (!empty($linkAtrr['attribs']))
					$attribs = ' ' . JArrayHelper::toString($linkAtrr['attribs']);
				else
					$attribs = '';
				echo "<link href=\"" . $link . "\" " . $linkAtrr['relType'] . "=\"" . $linkAtrr['relation'] . "\"" . $attribs . " />\n";
			}
		}

		// On traite les fichiers.
		if (count($doc->_styleSheets)) {

			$css_exclude = explode(',', $this->params->get('css_exclude'));
			foreach ($css_exclude as $k => $v) {
				$css_exclude[$k] = trim($v);
			}

			foreach($doc->_styleSheets as $source => $attribs) {
				$path = str_replace(JUri::root(), '/', $source);
				$file = substr($source, strrpos($source, '/')+1);

				// On exclut les fichiers.
				if (in_array($file, $css_exclude)) {
					continue;
				}

				// On remplace les scripts des modules par ceux du template s'ils existent.
				if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/css/') !== false || strpos($source, 'media/system/css/') !== false) {

					$min_css_path = $template_path . '/custom' . str_replace('.css', '.min.css', $path);
					$css_path = $template_path . '/custom' . $path;

					// On regarde si une version minimisée existe.
					if (JFile::exists($min_css_path)) {
						$source = $template_uri . '/custom' . str_replace('.css', '.min.css', $path);
					} elseif (JFile::exists($css_path)) { // On regarde pour la version normale.
						$source = $template_uri . '/custom' . $path;
					}

				}

				// On ajoute le script.
				echo "<link rel=\"stylesheet\" href=\"" . $source . "\">\n";
			}
		}

		if (!empty($doc->_style)) {
			foreach ($doc->_style as $type => $content) {
				if (!empty($content)) {
					echo "<style type=\"" . $type . "\">\n" . $content . "</style>\n";
				}
			}
		}

		if ($this->params->get('modernizr', false) && !$this->params->get('is_mobile', false))
			echo "<script src=\"" . JURI::root(true) . "/plugins/system/etdoptimizer/js/modernizr.custom.44931.js\"></script>";

		return ob_get_clean();
		
	}
	
}