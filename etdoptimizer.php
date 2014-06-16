<?php
/**
 * @package		Etudoo - ETD Solutions
 * @subpackage	Plugin ETDOptimizer
 * 
 * @version		1.1.1
 * @copyright	Copyright (C) 2012 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license		http://www.etd-solutions.com/licence
 * @author		ETD Solutions http://www.etd-solutions.com
**/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.registry.registry');

/**
 * Plugin pour optimiser le rendu des pages.
 *
 * @package		EtdSolutions.Plugin
 * @subpackage	plg_system_etdoptimizer
 */
class plgSystemEtdOptimizer extends JPlugin {

	public function onAfterRoute() {

        /**
         * @var JSite $app
         */
        $app = JFactory::getApplication();

		// On utilise ce plugin que dans l'application Site.
		if ($app->isAdmin()) {
			return true;
		}
	    
	    // Si la détection mobile est activée
	    if ($this->params->get('mobile_enabled', 0)) {

            // On récupère les URI.
            $currentUri = JUri::getInstance();
            $mobileUri = new JUri($this->params->get('mobile_uri'));
	    
	    	// On importe le détecteur.
	    	if(!class_exists('uagent_info')){
	    		require_once(JPATH_PLUGINS.'/system/etdoptimizer/lib/mdetect.php');
			}

            // On instancie le détecteur.
            $ua = new uagent_info();

            // 3 conditions pour changer le cours de l'histoire :
            //  - on est sur un mobile
            //  - on est sur une tablette et on a activé leur gestion
            //  - on est déjà sur l'url mobile
            if (($ua->isMobilePhone || $ua->isTierIphone ) || ( $this->params->get('mobile_tablets', 0) &&  $ua->isTierTablet) || ( $currentUri->getHost() == $mobileUri->getHost() ) ) {
                $this->params->set('is_mobile', true);

                // Si on a activé la redirection.
                if ($this->params->get('mobile_redirect', 0)) {

                    // Si ce n'est pas le même hôte on redirige vers la version mobile.
                    if ($currentUri->getHost() != $mobileUri->getHost()) {
                        $app->redirect($mobileUri->toString());
                        return true;
                    }

                }

                // On change de template.
                $template = $this->params->get('mobile_template', '');
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
		
		// On récupère toutes les parties gérées par le plugin.
		$files = JFolder::files(JPATH_PLUGINS.'/system/etdoptimizer/parts', '.php$');
		if ($files) {

			// On initialise le tableau de remplacement.
			$replace = array();

			foreach($files as $file) {
				$name = strtolower(JFile::stripExt($file));
				
				// On vérifie que l'on a besoin de bosser.
				if (strpos($body,'<etdoptimizer:'.$name) === false) {
					break;
				}	
							
				$instance = $this->getPart($name);
				if ($instance) {
					$replace['/<etdoptimizer:'.$name.'\s*?\/>/i'] = $instance->render();
				} else {
					$replace['/<etdoptimizer:'.$name.'\s*?\/>/i'] = '<!-- impossible de trouver la partie ' . $name . ' -->';
				}
			}
			
			// On remplace les occurences.
			$body = preg_replace(array_keys($replace), $replace, $body);
			
			// On remplace les balises non gérées.
			$body = preg_replace('/<etdoptimizer:[a-z]*?(\s*)?\/>/i', '', $body);

            // On vire tous les scripts liés à Mootools.
            if ($this->params->get('mootools_legacy', '0') == '0' && version_compare( JVERSION, '3.0', '<' )) {
                $body = preg_replace("/(new JCaption\()(.*)(\);)/isU", "", $body);
                $body = preg_replace("/(window.addEvent\()(.*)(\);)/isU", "", $body);
            }
			
			// On définit la réponse.
			JResponse::setBody($body);
		}
		
	}
	
	protected function getPart($type) {

		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$partClass = 'EtdOptimizer' . ucfirst($type);
		$folder = JPATH_PLUGINS.'/system/etdoptimizer/parts';

		if (!class_exists($partClass)) {

			jimport('joomla.filesystem.path');
			$path = JPath::find($folder, $type.'.php');
			if ($path) {
				require_once $path;

				if (!class_exists($partClass)) {
					throw new RuntimeException(JText::sprintf('Classe part non trouvée : %s', $partClass), 500);
				}
			} else {
				throw new RuntimeException(JText::sprintf('Classe part non trouvée : %s', $partClass), 500);
			}
		}

		return new $partClass($this->params);
	}
	
}
	