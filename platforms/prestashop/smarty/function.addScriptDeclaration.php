<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

function smarty_function_addScriptDeclaration($params, Smarty_Internal_Template $template) {

    $content = isset($params['content']) ? trim($params['content']) : '';
    $type    = isset($params['type']) ? trim($params['type']) : 'text/javascript';

    if (!empty($content)) {
        EtdOptimizer::addScriptDeclaration($content, $type);
    }

}
