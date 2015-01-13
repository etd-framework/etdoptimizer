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

define('PARAM_JQUERY', 'ETDOPTIMIZER_JQUERY');
define('PARAM_MODERNIZR', 'ETDOPTIMIZER_MODERNIZR');
define('PARAM_JS_EXCLUDE', 'ETDOPTIMIZER_JS_EXCLUDE');
define('PARAM_CSS_EXCLUDE', 'ETDOPTIMIZER_CSS_EXCLUDE');
define('PARAM_VIEWPORT', 'ETDOPTIMIZER_VIEWPORT');
define('PARAM_MOOTOOLS_LEGACY', '');
define('PARAM_IS_MOBILE', 'ETDOPTIMIZER_IS_MOBILE');
define('PARAM_MOBILE_ENABLED', 'ETDOPTIMIZER_MOBILE_ENABLED');
define('PARAM_MOBILE_URI', 'ETDOPTIMIZER_MOBILE_URI');
define('PARAM_MOBILE_TABLETS', 'ETDOPTIMIZER_MOBILE_TABLETS');
define('PARAM_MOBILE_REDIRECT', 'ETDOPTIMIZER_MOBILE_REDIRECT');
define('PARAM_MOBILE_TEMPLATE', 'ETDOPTIMIZER_MOBILE_TEMPLATE');
define('PARAM_MINIFY', 'ETDOPTIMIZER_MINIFY');

/**
 * Module pour optimiser le rendu HTML des pages.
 */
class EtdOptimizer extends Module {

    private $helper;

    public function __construct() {

        $this->name = 'etdoptimizer';
        $this->tab = 'others';
        $this->version = '2.0';
        $this->author = 'ETD Solutions';
        $this->need_instance = 1;

        parent::__construct();

        $this->displayName = $this->l('ETD Optimizer');
        $this->description = $this->l('Optimize html code rendering.');

        $this->helper = new EtdOptimizerHelper(
            $this->loadParams(),
            $this->local_path,
            $this->_path."vendor",
            _PS_BASE_URL_,
            _PS_THEME_DIR_,
            _THEME_DIR_
        );
    }

    public function install() {

        Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MODERNIZR', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MINIFY', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_JS_EXCLUDE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_CSS_EXCLUDE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_ENABLED', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_URI', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TABLETS', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_REDIRECT', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TEMPLATE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_VIEWPORT', 'width=device-width, initial-scale=1.0');

        return (parent::install() && $this->registerHook('displayEtdOptimizerHead') && $this->registerHook('displayEtdOptimizerScripts'));

    }

    public function uninstall() {

        Configuration::deleteByName('ETDOPTIMIZER_MODERNIZR');
        Configuration::deleteByName('ETDOPTIMIZER_JQUERY');
        Configuration::deleteByName('ETDOPTIMIZER_MINIFY');
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

    public function hookDisplayEtdOptimizerHead() {

        $this->helper->updateDoc(
            'utf-8',
            $this->context->smarty->tpl_vars['meta_title']->value,
            $this->context->smarty->tpl_vars['meta_description']->value,
            $this->context->smarty->tpl_vars['meta_keywords']->value,
            null,
            $this->context->smarty->tpl_vars['css_files']->value,
            null,
            $this->context->smarty->tpl_vars['js_files']->value,
            $this->context->smarty->tpl_vars['js_inline']->value
        );

        $head = $this->helper->getPart('head');

        return $head->render();

    }

    public function hookDisplayEtdOptimizerScripts() {

        $scripts = $this->helper->getPart('scripts');

        return $scripts->render();

    }

    protected function loadParams() {

        return Configuration::getMultiple(array(
            'ETDOPTIMIZER_JQUERY',
            'ETDOPTIMIZER_MODERNIZR',
            'ETDOPTIMIZER_MINIFY',
            'ETDOPTIMIZER_JS_EXCLUDE',
            'ETDOPTIMIZER_CSS_EXCLUDE',
            'ETDOPTIMIZER_MOBILE_ENABLED',
            'ETDOPTIMIZER_MOBILE_URI',
            'ETDOPTIMIZER_MOBILE_TABLETS',
            'ETDOPTIMIZER_MOBILE_REDIRECT',
            'ETDOPTIMIZER_MOBILE_TEMPLATE',
            'ETDOPTIMIZER_VIEWPORT'
        ), null, 0, 0);

    }

}