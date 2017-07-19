<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

class EtdOptimizerScripts extends EtdOptimizerPart {

    public function render() {

        $template_path = $this->helper->getTemplatePath();
        $template_uri = $this->helper->getTemplateUri();

        ob_start();

        // jQuery
        if ($this->helper->getParam(PARAM_JQUERY)) {
            if ($this->helper->getParam(PARAM_REQUIREJS)) {
                EtdOptimizerRequireJS::addModule("jquery", $this->helper->getVendorURI() . "etdsolutions/jquery/jquery.min.js");
            } else {
                echo "<script src=\"https://cdn.rawgit.com/etd-framework/jquery/1.x-master/jquery.min.js\"></script>\n";
                echo "<script>window.jQuery || document.write('<script src=\"" . $this->helper->getVendorURI() . "etdsolutions/jquery/jquery.min.js\"><\\/script>')";
                if ($this->helper->getParam(PARAM_JQUERY_NOCONFLICT)) {
                    echo ";jQuery.noConflict()";
                }
                echo "</script>\n";
            }
        }

        // requireJS
        if ($this->helper->getParam(PARAM_REQUIREJS)) {
            echo "<script src=\"" . $this->helper->getVendorURI() . "etdsolutions/requirejs/require.min.js\"></script>\n";
            echo "<script>\n" . EtdOptimizerRequireJS::render($this->helper) . "</script>\n";
        }

        // On traite les fichiers.
        $docScripts = $this->helper->getDocScripts();
        if (!empty($docScripts)) {

            $js_exclude = explode(',', $this->helper->getParam(PARAM_JS_EXCLUDE));
            $js_exclude = array_map('trim', $js_exclude);

            if (!$this->helper->getParam(PARAM_MOOTOOLS_LEGACY)) {
                $js_exclude = array_merge($js_exclude, array('mootools-core.js', 'core.js', 'mootools-more.js', 'caption.js', 'modal.js', 'mootools.js', 'mootools-core-uncompressed.js', 'mootools-more-uncompressed.js', 'core-uncompressed.js', 'caption-uncompressed.js'));
            }

            if ($this->helper->getParam(PARAM_JQUERY)) {
                $js_exclude = array_merge($js_exclude, array('jquery-1.11.0.min.js', 'jquery-migrate-1.2.1.min.js', 'jquery.min.js', 'jquery-noconflict.js', 'jquery-migrate.min.js'));
            }

            foreach ($docScripts as $source => $attribs) {

                // On récupère le chemin et nom de fichier depuis l'URL.
                $path = str_replace($this->helper->getRootURI(), '/', $source);
                $file = substr($source, strrpos($source, '/')+1);

                // On retire les fichiers exclus.
                if (in_array($file, $js_exclude)) {
                    continue;
                }

                // On remplace les scripts des modules par ceux du template s'ils existent.
                if (strpos($source, 'modules/') !== false || strpos($source, 'media/jui/js/') !== false || strpos($source, 'media/system/js/') !== false || strpos($source, 'js/') !== false) {
                    if (file_exists($template_path . 'custom' . $path)) {
                        $source = $template_uri . 'custom' . $path;
                        $path = str_replace($this->helper->getRootURI(), '/', $source);
                    }
                }

                // On regarde si une version minimisée existe.
                if (file_exists($this->helper->getRootPath().str_replace('.js', '.min.js', substr($path, 1)))) {
                    $source = str_replace('.js', '.min.js', $source);
                }

		        // On compile les attributs
                $str_attribs = "";
                if (isset($attribs["async"]) && $attribs["async"]) {
                    $str_attribs .= " async";
                }
                if (isset($attribs["defer"]) && $attribs["defer"]) {
                    $str_attribs .= " defer";
                }

                // On ajoute le script.
                echo "<script src=\"" . $source . "\"" . $str_attribs . "></script>\n";

            }
        }

        // On traite les scripts en ligne.
        $docScript = $this->helper->getDocScript();
        if (!empty($docScript)) {
            $script = "";
            foreach ($docScript as $content) {
                $script .= $content . "\n";
            }
            if (!empty($script)) {
                if ($this->helper->getParam(PARAM_MINIFY)) {
                    $minifier = new MatthiasMullie\Minify\JS($script);
                    $script = $minifier->minify();
                }
                echo "<script>" . $script . "</script>";
            }
        }

        return ob_get_clean();

    }

}
