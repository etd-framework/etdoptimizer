<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.0
 * @copyright    Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

if (!defined('_CAN_LOAD_FILES_')) exit;

/**
 * Module pour optimiser le rendu HTML des pages.
 */
class EtdOptimizer extends Module {

    public function __construct() {

        $this->name = 'etdoptimizer';
        $this->tab = 'others';
        $this->version = '2.0';
        $this->author = 'ETD Solutions';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('ETD Optimizer');
        $this->description = $this->l('Optimize html code rendering.');

        // On détecte les hooks disponibles.
        $this->populateHooks();

    }

    /**
     * Méthode magique pour appeler pouvoir appeler n'importe quel hook sur ce module.
     *
     * @param $method Nom de la méthode appellée
     * @param $args   Paramètres passées à la méthode
     *
     * @return mixed  Le résultat de la méthode ou false en cas d'erreur
     */
    public function __call($method, $args) {

        // Si la méthode existe, on l'appel.
        if (function_exists($method)) {
            return call_user_func_array($method, $args);
        } elseif ($this->isRegisteredHook($method)) { // C'est un hook détecté !
            return $this->executeHook($method, $args);
        }

        return false;
    }

    public function install() {

        Configuration::updateGlobalValue('ETDOPTIMIZER_MODERNIZR', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_JS_EXCLUDE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_CSS_EXCLUDE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_ENABLED', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TEMPLATE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TABLETS', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_REDIRECT', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_URI', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_VIEWPORT', 'width=device-width, initial-scale=1.0');

        return (parent::install() && $this->registerHook('displayHeader') && $this->registerHook('displayHome') && $this->registerHook('actionDispatcher'));

    }

    public function uninstall() {

        Configuration::deleteByName('ETDOPTIMIZER_MODERNIZR');
        Configuration::deleteByName('ETDOPTIMIZER_JQUERY');
        Configuration::deleteByName('ETDOPTIMIZER_JS_EXCLUDE');
        Configuration::deleteByName('ETDOPTIMIZER_CSS_EXCLUDE');
        Configuration::deleteByName('ETDOPTIMIZER_MOBILE_ENABLED');
        Configuration::deleteByName('ETDOPTIMIZER_MOBILE_TEMPLATE');
        Configuration::deleteByName('ETDOPTIMIZER_MOBILE_TABLETS');
        Configuration::deleteByName('ETDOPTIMIZER_MOBILE_REDIRECT');
        Configuration::deleteByName('ETDOPTIMIZER_MOBILE_URI');
        Configuration::deleteByName('ETDOPTIMIZER_VIEWPORT');

        return parent::uninstall();
    }

    public function hookDisplayHeader($params) {

        $this->clearFiles();

        // On récupère les scripts.
        $js_files = $this->context->controller->js_files;

        // Les JS persos en premier.
        array_unshift($js_files, __PS_BASE_URI__ . 'modules/' . $this->name . '/vendor/etdsolutions/jquery/jquery.min.js');

        // On remplace la pile de scripts.
        $this->context->controller->js_files = $js_files;

    }

    /**
     * Hook appelé avant l'exécution du controller.
     *
     * @param $params
     */
    public function hookActionDispatcher($params) {

        // On utilise ce plugin que dans la partie Front.
        if (isset($this->context->employee)) {
            return;
        }

        // Si la détection mobile est activée
        if (Configuration::get('ETDOPTIMIZER_MOBILE_ENABLED')) {

            // On récupère les URI.
            $https = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 's://' : '://';
            $currentUri = parse_url('http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $mobileUri  = parse_url(Configuration::get('ETDOPTIMIZER_MOBILE_URI'));

            // On instancie le détecteur.
            $mobile_detect = $this->context->getMobileDetect();

            // 3 conditions pour changer le cours de l'histoire :
            //  - on est sur un mobile
            //  - on est sur une tablette et on a activé leur gestion
            //  - on est déjà sur l'url mobile
            if ($mobile_detect->isMobile() || (Configuration::get('ETDOPTIMIZER_MOBILE_TABLETS') && $mobile_detect->isTablet()) || ($currentUri['host'] == $mobileUri['host'])) {

                // Si on a activé la redirection.
                if (Configuration::get('ETDOPTIMIZER_MOBILE_REDIRECT')) {

                    // Si ce n'est pas le même hôte on redirige vers la version mobile.
                    if ($currentUri['host'] != $mobileUri['host']) {
                        Tools::redirect(Configuration::get('ETDOPTIMIZER_MOBILE_URI'));
                        return;
                    }

                }

                // On change de template.
                $template = Configuration::get('ETDOPTIMIZER_MOBILE_TEMPLATE');
                if (!empty($template)) {
                    $this->context->controller->setTemplate($template);
                    $this->context->controller->setMobileTemplate($template);
                    $this->context->controller->useMobileTheme();
                }

            }

        }

        // Chemin vers le modifier smarty.
        $plugins_path = _PS_ALL_THEMES_DIR_ . $this->context->shop->theme_directory . '/plugins/';

        // S'il existe.
        if (is_dir($plugins_path)) {
            $this->context->smarty->addPluginsDir($plugins_path);
        }
    }

    public function hookDisplayHome($params) {

        $this->clearFiles();
    }

    /**
     * Méthode pour optimiser les scripts.
     */
    protected function clearFiles() {

        $js_excludes = array('jquery-1.11.0.min.js','jquery-migrate-1.2.1.min.js','tools.js','jquery.easing.js','jquery-1.7.2.min.js', 'jquery.idTabs.js', 'jquery.autocomplete.js', 'jquery.bxSlider.min.js', 'homeslider.js', 'jquery.fancybox.js', 'jquery.scrollTo.js', 'jquery.serialScroll.js', 'crossselling.js', 'jquery.typewatch.js');
        $css_excludes = array('blockcart.css', 'blocksearch.css', 'bx_styles.css', 'jquery.autocomplete.css', 'jquery.fancybox.css', 'crossselling.css');

        // On récupère les scripts.
        $js_files = $this->context->controller->js_files;

        foreach ($js_files as $k => $v) {
            $path = substr($v, 1);
            $file = substr($v, strrpos($v, '/') + 1);

            // On exclut les fichiers.
            if (in_array($file, $js_excludes)) {
                unset($js_files[$k]);
            } elseif (strpos($v, 'modules/') !== false || strpos($v, 'jquery/plugins/') !== false) { // On remplace les scripts des modules par ceux du template s'ils existent.

                // On regarde si une version minimisée existe.
                $uri = Media::getJSPath(_THEME_DIR_ . str_replace('.js', '.min.js', $path));

                // On regarde pour la version normale.
                if (!$uri) {
                    $uri = Media::getJSPath(_THEME_DIR_ . $path);
                }

                if ($uri) { // Si on a trouvé un script, on remplace.
                    $js_files[$k] = $uri;
                }
            } elseif ($uri = Media::getJSPath(str_replace('.js', '.min.js', $v))) { // On regarde s'il existe une version minimisée.
                $js_files[$k] = $uri;
            }

        }

        // On remplace la pile de scripts.
        $this->context->controller->js_files = $js_files;

        // CSS on the top !
        $css_files = $this->context->controller->css_files;
        foreach ($css_files as $k => $v) {
            $file = substr($k, strrpos($k, '/') + 1);
            if (in_array($file, $css_excludes)) {
                unset($css_files[$k]);
            } elseif ($uri = Media::getCSSPath(str_replace('.css', '.min.css', $v))) { // On regarde s'il existe une version minimisée.
                $css_files[$k] = $uri;
            }
        }
        $this->context->controller->css_files = $css_files;

    }

    /**
     * Méthode pour détecter tous les hooks d'affichage disponibles.
     */
    protected function populateHooks() {

        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = 'SELECT name FROM `' . _DB_PREFIX_ . 'hook` ORDER BY `name`';
        $hooks = $db->executeS($sql);

        if (count($hooks)) {
            foreach ($hooks as $hook) {
                $this->hooks[] = $hook['name'];
            }
        }

    }

    protected function isRegisteredHook($method) {

        // On récupère le nom du hook.
        $method = str_replace('hook', '', $method);

        // On contrôle si le hook est disponible
        return in_array($method, $this->hooks);

    }

}