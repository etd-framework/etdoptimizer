<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

class EtdOptimizerStylesheets extends EtdOptimizerPart {

    public function render() {

        $template_path = $this->helper->getTemplatePath();
        $template_uri = $this->helper->getTemplateUri();

        ob_start();

        // On traite les feuilles de styles qui doivent s'afficher en bas de page seulement.
        $docStylesheets = $this->helper->getDocStylesheets();
        if (!empty($docStylesheets)) {

            // On traite les priorités
            uasort($docStylesheets, function($a, $b) {
                $a_priority = isset($a['options']['priority']) ? (int) $a['options']['priority'] : 0;
                $b_priority = isset($b['options']['priority']) ? (int) $b['options']['priority'] : 0;
                if ($a_priority == $b_priority) {
                    return 0;
                }
                return ($a_priority > $b_priority) ? -1 : 1;
            });

            $css_exclude = explode(',', $this->helper->getParam(PARAM_CSS_EXCLUDE));
            $css_exclude = array_map('trim', $css_exclude);

            foreach($docStylesheets as $source => $attribs) {

                // En bas de page seulement.
                if (isset($attribs['attribs']) && isset($attribs['attribs']['bottom']) && $attribs['attribs']['bottom']) {

                    // On récupère le chemin et nom de fichier depuis l'URL.
                    $path = str_replace($this->helper->getRootURI(), '/', $source);
                    $file = substr($source, strrpos($source, '/')+1);
                    if (strpos($file, "?") !== false) {
                        $file = substr($file, 0, strrpos($file, '?'));
                    }

                    // On retire les fichiers exclus.
                    if (in_array($file, $css_exclude) || in_array($path, $css_exclude)) {
                        continue;
                    }

                    // On remplace les scripts des modules par ceux du template s'ils existent.
                    if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/css/') !== false || strpos($source, 'media/system/css/') !== false) {

                        // On regarde si une version minimisée existe.
                        if (file_exists($template_path . 'custom' . $path)) {
                            $source = $template_uri . 'custom' . $path;
                        }

                    }

                    // On regarde si une version minimisée existe.
                    if (file_exists($this->helper->getRootPath().str_replace('.css', '.min.css', substr($path, 1)))) {
                        $source = str_replace('.css', '.min.css', $source);
                    }

                    echo "<link rel=\"stylesheet\" href=\"" . $source . "\">\n";
                }

            }

        }

        return ob_get_clean();

    }

}
