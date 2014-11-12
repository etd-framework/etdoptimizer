<?php
/**
 * @package        ETD Solutions
 * @subpackage     Plugin ETDOptimizer
 *
 * @version        1.1.1
 * @copyright      Copyright (C) 2012 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license        http://www.etd-solutions.com/licence
 * @author         ETD Solutions http://www.etd-solutions.com
 **/

require_once(JPATH_PLUGINS . '/system/etdoptimizer/part.php');

class EtdOptimizerScripts extends EtdOptimizerPart {

    const JQUERY_VERSION = "1.11.1";

    public function render() {

        jimport('joomla.filesystem.file');
        $doc           = JFactory::getDocument();
        $template      = JFactory::getApplication()
                                 ->getTemplate();
        $template_path = JPATH_THEMES . '/' . $template;
        $template_uri  = JUri::root(true) . '/templates/' . $template;
        ob_start();

        if ($this->params->get('jquery', false)) {
            echo "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/" . self::JQUERY_VERSION . "/jquery.min.js\"></script>\n<script>window.jQuery || document.write('<script src=\"" . JURI::root(true) . "/plugins/system/etdoptimizer/js/jquery-" . self::JQUERY_VERSION . ".min.js\"><\\/script>')</script>\n";

            // Si on est sur du Joomla! 3.x on vire le jQuery livré avec.
            if (version_compare(JVERSION, '3.0', '>=')) {
                $toUnset = array(
                    '/media/jui/js/jquery.min.js',
                    '/media/jui/js/jquery-noconflict.js',
                    '/media/jui/js/jquery-migrate.min.js',
                );
                foreach($toUnset as $key) {
                    unset($doc->_scripts[JUri::base(true) . $key]);
                }
            }
        }

        // On traite les fichiers.
        if (count($doc->_scripts)) {

            $js_exclude = array_map('trim', explode(',', $this->params->get('js_exclude')));

            if ($this->params->get('mootools_legacy', '0') == '0') {
                $js_exclude = array_merge($js_exclude, array('mootools-core.js', 'core.js', 'mootools-more.js', 'caption.js', 'modal.js', 'mootools.js', 'mootools-core-uncompressed.js', 'mootools-more-uncompressed.js', 'core-uncompressed.js', 'caption-uncompressed.js'));
            }

            foreach ($doc->_scripts as $source => $attribs) {
                $path = str_replace(JUri::root(), '/', $source);
                $file = substr($source, strrpos($source, '/') + 1);

                // On exclut les fichiers.
                if (in_array($file, $js_exclude)) {
                    continue;
                }

                // On remplace les scripts des modules par ceux du template s'ils existent.
                if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/js/') !== false || strpos($source, 'media/system/js/') !== false) {

                    $min_js_path = $template_path . '/custom' . str_replace('.js', '.min.js', $path);
                    $js_path     = $template_path . '/custom' . $path;

                    // On regarde si une version minimisée existe.
                    if (JFile::exists($min_js_path)) {
                        $source = $template_uri . '/custom' . str_replace('.js', '.min.js', $path);
                    } elseif (JFile::exists($js_path)) { // On regarde pour la version normale.
                        $source = $template_uri . '/custom' . $path;
                    }

                }

                // On ajoute le script.
                echo "<script src=\"" . $source . "\"></script>\n";
            }
        }

        // On traite les scripts en ligne.
        if (count($doc->_script)) {
            $script = "";
            foreach ($doc->_script as $content) {
                $script .= $content . "\n";
            }
            if (!empty($script)) {
                echo "<script>" . $script . "</script>";
            }
        }

        return ob_get_clean();

    }

}