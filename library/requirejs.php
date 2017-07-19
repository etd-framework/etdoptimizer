<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

class EtdOptimizerRequireJS {

    const JSON_ENCODE_FLAGS = JSON_UNESCAPED_SLASHES;

    /**
     * @var array Scripts JS en ligne exécutés quand le DOM est chargé.
     */
    protected static $domReadyJs = [];

    /**
     * @var array Scripts JS en ligne exécutés dans le contexte RequireJS.
     */
    protected static $requireJS = [];

    /**
     * @var array Les modules et leur chemin à charger dans RequireJS.
     */
    protected static $requireModules = [];

    /**
     * @var array Les packages à charger dans RequireJS.
     */
    protected static $requirePackages = [];

    /**
     * @var array Les packages à charger dans RequireJS.
     */
    protected static $requireMap = [];

    public static function addDomReadyJS($script, $onTop = false, $modules = '', $jQuery = false) {

        $module = $jQuery === true ? "jquery" : "";

        if (strlen($modules) > 0) {

            if (strlen($module) > 0) {
                $module .= ", ";
            }

            $module .= $modules;
        }

        if (strlen($module) > 0) {
            $module .= ", ";
        }

        $module .= "domReady!";

        self::addJS($module, $script, $onTop);

    }

    public static function addModule($module, $path, $shim = false, $deps = null, $exports = null, $init = null) {

        if (!isset(self::$requireModules[$module])) {
            self::$requireModules[$module] = array();
        }

        self::$requireModules[$module]['module'] = $module;
        self::$requireModules[$module]['path']   = $path;
        self::$requireModules[$module]['shim']   = false;

        if ($shim) {
            $shim = array();
            if (isset($deps)) {
                $shim["deps"] = $deps;
            }
            if (isset($exports)) {
                $shim["exports"] = $exports;
            }
            if (isset($init)) {
                $shim["init"] = $init;
            }
            self::$requireModules[$module]['shim'] = $shim;
        }

    }

    public static function addPackage($package, $location = null, $main = null) {

        $package = strtolower($package);

        if (!array_key_exists($package, self::$requirePackages)) {

            if (isset($location) || isset($main)) {

                $arr = [
                    "name" => $package
                ];

                if (isset($location)) {
                    $arr["location"] = $location;
                }

                if (isset($main)) {
                    $arr["main"] = $main;
                }

            } else {
                $arr = $package;
            }

            self::$requirePackages[$package] = $arr;
        }

    }

    public static function addMap($prefix, $old, $new) {

        if (!array_key_exists($prefix, self::$requireMap)) {
            self::$requireMap[$prefix] = [];
        }

        self::$requireMap[$prefix][$old] = $new;

    }

    public static function addJS($module, $script = '', $onTop = false) {

        if (strpos($module, ' ') !== false) {
            $module = str_replace(' ', '', $module);
        }

        if (!isset(self::$requireJS[$module])) {
            self::$requireJS[$module] = array();
        }

        if (!in_array($script, self::$requireJS[$module])) {
            if ($onTop) {
                array_unshift(self::$requireJS[$module], $script);
            } else {
                array_push(self::$requireJS[$module], $script);
            }
        }

    }

    public static function render(EtdOptimizerHelper $helper) {

        $js = "";

        // On ajoute le domReady si nécessaire.
        if ($helper->getParam(PARAM_DOMREADY)) {
            self::addModule('domReady', $helper->getVendorURI() . 'js/vendor/domReady.min');
        }

        // On crée la configuration de requireJS
        $js .= "requirejs.config({\n";
        $js .= "\tbaseUrl: '" . $helper->getRootURI() . "'";

        // Debug => cache bust
        if ($helper->getDebug()) {
            $js .= ",\turlArgs: 'bust=' +  (new Date()).getTime(),\n";
            $js .= "\twaitSeconds: 0";
        }

        // map
        if (count(self::$requireMap)) {
            $js .= ",\n\tmap: " . json_encode(self::$requireMap, self::JSON_ENCODE_FLAGS);
        }

        // packages
        if (count(self::$requirePackages)) {
            $js .= ",\n\tpackages: " . json_encode(array_values(self::$requirePackages), self::JSON_ENCODE_FLAGS);
        }

        // modules
        if (count(self::$requireModules)) {

            $shim  = [];
            $paths = [];
            foreach (self::$requireModules as $module) {
                $paths[] = "\t\t" . json_encode($module['module'], self::JSON_ENCODE_FLAGS) . ": " . json_encode($module['path'], self::JSON_ENCODE_FLAGS);
                if ($module['shim'] !== false) {
                    $shim[] = "\t\t" . json_encode($module['module'], self::JSON_ENCODE_FLAGS) . ": " . json_encode($module['shim'], self::JSON_ENCODE_FLAGS);
                }
            }

            if (count($shim)) {
                $js .= ",\n\tshim: {\n";
                $js .= implode(",\n", $shim) . "\n";
                $js .= "\t}";
            }
            if (count($paths)) {
                $js .= ",\n\tpaths: {\n";
                $js .= implode(",\n", $paths) . "\n";
                $js .= "\t}";
            }

        }

        $js .= "\n});\n";

        if (count(self::$requireJS)) {

            foreach (self::$requireJS as $id => $scripts) {

                $content      = "";
                $req_modules  = explode(",", preg_replace("/:[a-zA-Z]{1,}/", "", $id));
                $func_modules = explode(",", $id);

                foreach ($scripts as $script) {
                    if (!empty($script)) {
                        $content .= "  " . $script . "\n";
                    }
                }

                $js .= "require(" . json_encode($req_modules, self::JSON_ENCODE_FLAGS);

                if (!empty($content)) {
                    $modules = array_filter($func_modules, function ($module) {

                        return (strpos($module, '!') === false);
                    });
                    $modules = array_map(function ($module) {

                        if (strpos($module, ":") !== false) {
                            $module = substr($module, strrpos($module, ':') + 1);
                        }

                        if (strpos($module, '/') !== false) {
                            $module = substr($module, strrpos($module, '/') + 1);
                        }

                        if (strpos($module, '.js') !== false) {
                            $module = str_replace('.js', '', $module);
                        }

                        if (strpos($module, '.min') !== false) {
                            $module = str_replace('.min', '', $module);
                        }

                        if (strpos($module, '.') !== false) {
                            $module = substr($module, strrpos($module, '.') + 1);
                        }

                        $module = str_replace('-', '', $module);

                        return $module;
                    }, $modules);
                    $js .= ", function(" . implode(",", $modules) . ") {\n";
                    $js .= $content;
                    $js .= "}";
                }

                $js .= ");\n";

            }
        }

        // On minifie le JS.
        if ($helper->getParam(PARAM_MINIFY)) {

            if (!class_exists("\\MatthiasMullie\\Minify\\JS")) {
                throw new RuntimeException("Le paquet matthiasmullie/minify n'est pas installé. Exécuter &quot;composer require matthiasmullie/minify&quot;");
            }

            $minifier = new \MatthiasMullie\Minify\JS($js);
            $js = $minifier->minify();

        }

        return $js;

    }

}
