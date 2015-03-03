<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.0
 * @copyright    Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

// no direct access
defined('_JEXEC') or die;

define('PARAM_JQUERY', 'jquery');
define('PARAM_MODERNIZR', 'modernizr');
define('PARAM_JS_EXCLUDE', 'js_exclude');
define('PARAM_CSS_EXCLUDE', 'css_exclude');
define('PARAM_VIEWPORT', 'viewport');
define('PARAM_MOOTOOLS_LEGACY', 'mootools_legacy');
define('PARAM_IS_MOBILE', 'is_mobile');
define('PARAM_MOBILE_ENABLED', 'mobile_enabled');
define('PARAM_MOBILE_URI', 'mobile_uri');
define('PARAM_MOBILE_TABLETS', 'mobile_tablets');
define('PARAM_MOBILE_REDIRECT', 'mobile_redirect');
define('PARAM_MOBILE_TEMPLATE', 'mobile_template');
define('PARAM_MINIFY', 'minify');
define('PARAM_GOOGLE_FONTS', '');

/**
 * Plugin pour optimiser le rendu des pages.
 *
 * @package		EtdSolutions.Plugin
 * @subpackage	plg_system_etdoptimizer
 */
class plgSystemEtdOptimizer extends JPlugin {

	private $helper;

	function __construct(&$subject, $config = array()) {

        parent::__construct($subject, $config);

        $app = JFactory::getApplication();
		$template = $app->getTemplate();

		$this->helper = new EtdOptimizerHelper(
            $this->params->toArray(),
            JPATH_PLUGINS.'/system/etdoptimizer',
            JUri::root(true) . '/plugins/system/etdoptimizer/vendor',
            JUri::root(true),
            JPATH_THEMES . '/' . $template,
            JUri::root(true) . '/templates/' . $template,
            JPATH_ROOT
		);
	}

	public function onAfterRoute() {

        $app = JFactory::getApplication();

		// On utilise ce plugin que dans l'application Site.
		if ($app->isAdmin()) {
			return true;
		}

	    // Si la détection mobile est activée
	    if ($this->params->get(PARAM_MOBILE_ENABLED, 0)) {

            // On récupère les URI.
            $currentUri = JUri::getInstance();
            $mobileUri = new JUri($this->params->get(PARAM_MOBILE_URI));


            // 3 conditions pour changer le cours de l'histoire :
            //  - on est sur un mobile
            //  - on est sur une tablette et on a activé leur gestion
            //  - on est déjà sur l'url mobile
            if ($this->helper->isMobile($currentUri->get('host'))) {
                $this->params->set(PARAM_IS_MOBILE, true);

                // Si on a activé la redirection.
                if ($this->params->get(PARAM_MOBILE_REDIRECT, 0)) {

                    // Si ce n'est pas le même hôte on redirige vers la version mobile.
                    if ($currentUri->getHost() != $mobileUri->getHost()) {
                        $app->redirect($mobileUri->toString());
                        return true;
                    }

                }

                // On change de template.
                $template = $this->params->get(PARAM_MOBILE_TEMPLATE, '');
                if (!empty($template)) {
                    $app->setTemplate($template);
                }

			}

	    }

        return true;

	}

	/**
	 * Plugin qui modifie la page après le rendu.
	 */
	public function onAfterRender() {

		$app = JFactory::getApplication();

		// On utilise ce plugin que dans l'application Site.
		if ($app->isAdmin()) {
			return true;
		}

		// On récupère le corps de la réponse.
		$body = JResponse::getBody();

		// On vérifie que l'on a quelque chose à faire.
		if (strpos($body, '<etdoptimizer:') === false) {
			return true;
		}

        // On met à jours les infos dans le helper.
        $doc = JFactory::getDocument();
        $this->helper->updateDoc(
            $doc->getCharset(),
            $doc->getTitle(),
            $doc->getDescription(),
            $doc->getMetaData('keywords'),
            $doc->_links,
            $doc->_styleSheets,
            $doc->_style,
            $doc->_scripts,
            $doc->_script,
            $doc->_custom
        );

		// On récupère toutes les parties gérées par le plugin.
		$body = $this->helper->replaceParts($body);

		// On vire tous les scripts liés à Mootools.
		if ($this->params->get(PARAM_MOOTOOLS_LEGACY, '0') == '0' && version_compare( JVERSION, '3.0', '<' )) {
			$body = preg_replace("/(new JCaption\()(.*)(\);)/isU", "", $body);
			$body = preg_replace("/(window.addEvent\()(.*)(\);)/isU", "", $body);
		}

		// On définit la réponse.
		JResponse::setBody($body);

	}

}
	