<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.0
 * @copyright    Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/
class EtdOptimizerScripts extends EtdOptimizerPart {

    public function render() {

        $template_path = $this->helper->getTemplatePath();
        $template_uri = $this->helper->getTemplateUri();

        ob_start();

        if ($this->helper->getParam(PARAM_JQUERY)) {
            echo "<script src=\"https://raw.githubusercontent.com/etd-framework/jquery/1.x-master/jquery.min.js\"></script>\n<script>window.jQuery || document.write('<script src=\"" . $this->helper->getVendorURI() . "etdsolutions/jquery/jquery.min.js\"><\\/script>')</script>\n";
        }

        // On traite les fichiers.
        if ($this->helper->hasDocScripts()) {

            $js_exclude = explode(',', $this->helper->getParam(PARAM_JS_EXCLUDE));
            $js_exclude = array_map('trim', $js_exclude);

            if (!$this->helper->getParam(PARAM_MOOTOOLS_LEGACY)) {
                $js_exclude = array_merge($js_exclude, array('mootools-core.js', 'core.js', 'mootools-more.js', 'caption.js', 'modal.js', 'mootools.js', 'mootools-core-uncompressed.js', 'mootools-more-uncompressed.js', 'core-uncompressed.js', 'caption-uncompressed.js'));
            }

            foreach ($this->helper->getDocScripts() as $source => $attribs) {

                // On récupère le chemin et nom de fichier depuis l'URL.
                $path = str_replace($this->helper->getRootURI(), '/', $source);
                $file = substr($source, strrpos($source, '/')+1);

                // On retire les fichiers exclus.
                if (in_array($file, $js_exclude)) {
                    continue;
                }

                // On remplace les scripts des modules par ceux du template s'ils existent.
                if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/js/') !== false || strpos($source, 'media/system/js/') !== false) {

                    $min_js_path = $template_path . 'custom' . str_replace('.js', '.min.js', $path);
                    $js_path = $template_path . 'custom' . $path;

                    // On regarde si une version minimisée existe.
                    if (file_exists($min_js_path)) {
                        $source = $template_uri . 'custom' . str_replace('.js', '.min.js', $path);
                    } elseif (file_exists($js_path)) { // On regarde pour la version normale.
                        $source = $template_uri . 'custom' . $path;
                    }

                }

                // On ajoute le script.
                echo "<script src=\"" . $source . "\"></script>\n";
            }
        }

        // On traite les scripts en ligne.
        if ($this->helper->hasDocScript()) {
            $script = "";
            foreach ($this->helper->getDocScript() as $content) {
                $script .= $content . "\n";
            }
            if (!empty($script)) {
                if ($this->helper->getParam(PARAM_MINIFY)) {
                    $minifier = new Minify\JS($script);
                    $script = $minifier->minify();
                }
                echo "<script>" . $script . "</script>";
            }
        }

        return ob_get_clean();

    }

}