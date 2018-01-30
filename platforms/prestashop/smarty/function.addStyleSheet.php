<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

function smarty_function_addStyleSheet($params, Smarty_Internal_Template $template) {

    $src      = isset($params['src']) ? trim($params['src']) : '';
    $media    = isset($params['media']) ? trim($params['media']) : 'all';
	$priority = isset($params['priority']) ? $params['priority'] : null;

    if (!empty($src)) {
        EtdOptimizer::addStylesheet($src, $media, $priority);
    }

}
