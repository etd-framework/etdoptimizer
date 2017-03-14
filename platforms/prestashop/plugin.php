<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.5.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

if (!defined('_CAN_LOAD_FILES_')) exit;

define('PARAM_JQUERY', 'ETDOPTIMIZER_JQUERY');
define('PARAM_JQUERY_NOCONFLICT', 'ETDOPTIMIZER_JQUERY_NOCONFLICT');
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
define('PARAM_GOOGLE_FONTS', 'ETDOPTIMIZER_GOOGLE_FONTS');

/**
 * Module pour optimiser le rendu HTML des pages.
 */
class EtdOptimizer extends Module {

    private $helper;

    public static $stylesheets = array();
    public static $css = array();
    public static $js = array();
    public static $scripts = array();
    public static $custom = array();

    public function __construct() {

        $this->name = 'etdoptimizer';
        $this->tab = 'others';
        $this->version = '2.1.0';
        $this->author = 'ETD Solutions';
        $this->need_instance = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('ETD Optimizer');
        $this->description = $this->l('Optimize html code rendering.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->helper = new EtdOptimizerHelper(
            $this->loadParams(),
            $this->local_path,
            $this->_path."vendor",
            _PS_BASE_URL_,
            _PS_THEME_DIR_,
            _THEME_DIR_,
            _PS_ROOT_DIR_
        );
    }

    public function install() {

        Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY_NOCONFLICT', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MODERNIZR', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MINIFY', 1);
        Configuration::updateGlobalValue('ETDOPTIMIZER_JS_EXCLUDE', 'tools.js, jquery.easing.js');
        Configuration::updateGlobalValue('ETDOPTIMIZER_CSS_EXCLUDE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_ENABLED', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_URI', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TABLETS', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_REDIRECT', 0);
        Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TEMPLATE', '');
        Configuration::updateGlobalValue('ETDOPTIMIZER_VIEWPORT', 'width=device-width, initial-scale=1.0');
        Configuration::updateGlobalValue('ETDOPTIMIZER_GOOGLE_FONTS', '');

        return (parent::install() && $this->registerHook('actionDispatcher') && $this->registerHook('actionEtdOptimizerAddJS') && $this->registerHook('actionEtdOptimizerAddCSS') && $this->registerHook('actionEtdOptimizerAddScript') && $this->registerHook('actionEtdOptimizerAddStylesheet') && $this->registerHook('actionEtdOptimizerAddCustom') && $this->registerHook('displayEtdOptimizerHead') && $this->registerHook('displayEtdOptimizerScripts'));

    }

    public function uninstall() {

        foreach ($this->getConfigFields() as $field) {
            Configuration::deleteByName($field);
        }

        return parent::uninstall();
    }

    public function hookActionDispatcher() {

        // On ajoute le dossier des plugins Smarty.
        $this->context->smarty->addPluginsDir($this->local_path."platforms/prestashop/smarty");

    }

    public function hookActionEtdOptimizerAddCustom($params) {

        $custom = trim($params['custom']);

        if (!in_array($custom, self::$custom)) {
            self::$custom[] = $custom;
        }

    }

    public function hookActionEtdOptimizerAddJS($params) {

        if (!in_array($params['js'], self::$js)) {
            self::$js[] = $params['js'];
        }

    }

    public function hookActionEtdOptimizerAddScript($params) {

        self::$scripts[$params['src']] = '';

    }

    public function hookActionEtdOptimizerAddCSS($params) {

        if (!in_array($params['css'], self::$css)) {
            self::$css[] = $params['css'];
        }

    }

    public function hookActionEtdOptimizerAddStylesheet($params) {

        self::$stylesheets[$params['src']] = 'all';

    }

    public function hookDisplayEtdOptimizerHead() {

        $this->helper->updateDoc(
            'utf-8',
            $this->context->smarty->tpl_vars['meta_title']->value,
            $this->context->smarty->tpl_vars['meta_description']->value,
            $this->context->smarty->tpl_vars['meta_keywords']->value,
            null,
            function() {

                $context = Context::getContext();
                $css_files = array();
                if (array_key_exists('css_files', $context->smarty->tpl_vars)) {
                    $css_files = $context->smarty->tpl_vars['css_files']->value;
                }
                return array_merge($css_files, EtdOptimizer::$stylesheets);

            },
            function() {

                return EtdOptimizer::$css;

            },
            function() {

                $context = Context::getContext();
                $js_files = array();
                if (array_key_exists('js_files', $context->smarty->tpl_vars)) {
                    $js_files = $context->smarty->tpl_vars['js_files']->value;
                }
                return array_merge(array_flip($js_files), EtdOptimizer::$scripts);

            },
            function() {

                $context = Context::getContext();
                $js_inline = array();
                if (array_key_exists('js_inline', $context->smarty->tpl_vars)) {
                    $js_inline = $context->smarty->tpl_vars['js_inline']->value;
                }
                $js_inline = array_merge($js_inline, EtdOptimizer::$js);

                // JS Def
                $js_def = Media::getJsDef();
                $c = count($js_def);
                if ($c) {
                    $buffer = "var ";
                    $i = 0;
                    foreach ($js_def as $key => $value) {
                        $i++;
                        if (!empty($key)) {
                            $buffer .= $key ." = ";
                        }
                        $buffer .= json_encode($value);
                        if ($i < $c) {
                            $buffer .= ",\n";
                        }
                    }
                    $buffer .= ";\n";
                    array_unshift($js_inline, $buffer);
                }

                return $js_inline;
            },
            function () {

                return EtdOptimizer::$custom;

            },
            array()
        );

        $head = $this->helper->getPart('head');

        return $head->render();

    }

    public function hookDisplayEtdOptimizerScripts() {

        $scripts = $this->helper->getPart('scripts');

        return $scripts->render();

    }

    protected function loadParams() {

        return Configuration::getMultiple($this->getConfigFields(), null, 0, 0);

    }

    /** ADMINISTRATION **/

    public function getContent()  {

        $html = '';

        if (Tools::isSubmit('submitSettings'))  {

            Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY', (int)Tools::getValue('ETDOPTIMIZER_JQUERY'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_JQUERY_NOCONFLICT', (int)Tools::getValue('ETDOPTIMIZER_JQUERY_NOCONFLICT'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MODERNIZR', (int)Tools::getValue('ETDOPTIMIZER_MODERNIZR'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MINIFY', (int)Tools::getValue('ETDOPTIMIZER_MINIFY'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_JS_EXCLUDE', Tools::getValue('ETDOPTIMIZER_JS_EXCLUDE'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_CSS_EXCLUDE', Tools::getValue('ETDOPTIMIZER_CSS_EXCLUDE'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_ENABLED', (int)Tools::getValue('ETDOPTIMIZER_MOBILE_ENABLED'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_URI', Tools::getValue('ETDOPTIMIZER_MOBILE_URI'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TABLETS', (int)Tools::getValue('ETDOPTIMIZER_MOBILE_TABLETS'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_REDIRECT', (int)Tools::getValue('ETDOPTIMIZER_MOBILE_REDIRECT'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_MOBILE_TEMPLATE', Tools::getValue('ETDOPTIMIZER_MOBILE_TEMPLATE'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_VIEWPORT', Tools::getValue('ETDOPTIMIZER_VIEWPORT'));
            Configuration::updateGlobalValue('ETDOPTIMIZER_GOOGLE_FONTS', Tools::getValue('ETDOPTIMIZER_GOOGLE_FONTS'));

            $html .= $this->displayConfirmation($this->l('Configuration updated'));

        }

        $html .= $this->renderForm();

        return $html;
    }

    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('In-Page'),
                    'icon' => 'icon-file-text'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Inclure jQuery'),
                        'name' => 'ETDOPTIMIZER_JQUERY',
                        'desc' => 'Inclure la librairie JavaScript jQuery',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
	                    'type' => 'switch',
	                    'label' => $this->l('noConflict jQuery'),
	                    'name' => 'ETDOPTIMIZER_JQUERY_NOCONFLICT',
	                    'desc' => 'JQuery renonce au contrôle de la variable $',
	                    'values' => array(
		                    array(
			                    'id' => 'active_on',
			                    'value' => 1,
			                    'label' => $this->l('Enabled')
		                    ),
		                    array(
			                    'id' => 'active_off',
			                    'value' => 0,
			                    'label' => $this->l('Disabled')
		                    )
	                    )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Inclure Modernizr'),
                        'name' => 'ETDOPTIMIZER_MODERNIZR',
                        'desc' => 'Inclure Modernizr pour gérer HTML5/CSS3 pour les anciens navigateurs.',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Minifier le JS et CSS inline'),
                        'name' => 'ETDOPTIMIZER_MINIFY',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Fichiers JS à exclure'),
                        'name' => 'ETDOPTIMIZER_JS_EXCLUDE',
                        'desc' => $this->l('Liste séparée par virgule des noms de fichiers .js à exclure.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Fichiers CSS à exclure'),
                        'name' => 'ETDOPTIMIZER_CSS_EXCLUDE',
                        'desc' => $this->l('Liste séparée par virgule des noms de fichiers .css à exclure.')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $fields_form_2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Mobile'),
                    'icon' => 'icon-mobile'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Détection mobile'),
                        'name' => 'ETDOPTIMIZER_MOBILE_ENABLED',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Inclure les tablettes'),
                        'name' => 'ETDOPTIMIZER_MOBILE_TABLETS',
                        'desc' => $this->l('Inclure les tablettes comme périphériques mobiles'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Redirection'),
                        'name' => 'ETDOPTIMIZER_MOBILE_REDIRECT',
                        'desc' => $this->l('Rediriger le visiteur vers un site mobile'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('URL mobile'),
                        'name' => 'ETDOPTIMIZER_MOBILE_URI',
                        'desc' => $this->l('URL vers laquelle rediriger les mobiles')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $fields_form_3 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Avancé'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tag Viewport'),
                        'name' => 'ETDOPTIMIZER_VIEWPORT',
                        'desc' => $this->l('Valeur de la balise META Viewport')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Fonts'),
                        'name' => 'ETDOPTIMIZER_GOOGLE_FONTS',
                        'desc' => $this->l('Police Google Fonts à importer. (format: Bree+Serif|Great+Vibes')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1, $fields_form_2, $fields_form_3));
    }

    public function getConfigValues() {

        $ret = array();

        foreach ($this->getConfigFields() as $field) {
            $ret[$field] = Tools::getValue($field, Configuration::getGlobalValue($field));
        }

        return $ret;
    }

    protected function getConfigFields() {

        return array(
            'ETDOPTIMIZER_JQUERY',
            'ETDOPTIMIZER_JQUERY_NOCONFLICT',
            'ETDOPTIMIZER_MODERNIZR',
            'ETDOPTIMIZER_MINIFY',
            'ETDOPTIMIZER_JS_EXCLUDE',
            'ETDOPTIMIZER_CSS_EXCLUDE',
            'ETDOPTIMIZER_MOBILE_ENABLED',
            'ETDOPTIMIZER_MOBILE_URI',
            'ETDOPTIMIZER_MOBILE_TABLETS',
            'ETDOPTIMIZER_MOBILE_REDIRECT',
            'ETDOPTIMIZER_MOBILE_TEMPLATE',
            'ETDOPTIMIZER_VIEWPORT',
            'ETDOPTIMIZER_GOOGLE_FONTS',
        );

    }

}
