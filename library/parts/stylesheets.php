<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.6.0
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

            $css_exclude = explode(',', $this->helper->getParam(PARAM_CSS_EXCLUDE));
            $css_exclude = array_map('trim', $css_exclude);

            foreach($docStylesheets as $source => $attribs) {

                // En bas de page seulement.
                if (is_array($attribs['attribs']) && isset($attribs['attribs']['bottom']) && $attribs['attribs']['bottom']) {

                    // On récupère le chemin et nom de fichier depuis l'URL.
                    $path = str_replace($this->helper->getRootURI(), '/', $source);
                    $file = substr($source, strrpos($source, '/')+1);

                    // On retire les fichiers exclus.
                    if (in_array($file, $css_exclude) || in_array($path, $css_exclude)) {
                        continue;
                    }

                    // On remplace les scripts des modules par ceux du template s'ils existent.
                    if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/css/') !== false || strpos($source, 'media/system/css/') !== false) {

                        $min_css_path = $template_path . 'custom' . str_replace('.css', '.min.css', $path);
                        $css_path = $template_path . 'custom' . $path;

                        // On regarde si une version minimisée existe.
                        if (file_exists($min_css_path)) {
                            $source = $template_uri . 'custom' . str_replace('.css', '.min.css', $path);
                        } elseif (file_exists($css_path)) { // On regarde pour la version normale.
                            $source = $template_uri . 'custom' . $path;
                        }

                    }

                    echo "<link rel=\"stylesheet\" href=\"" . $source . "\">\n";
                }

            }

        }

        return ob_get_clean();

    }

}
