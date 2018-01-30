<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

function smarty_function_addMeta($params, Smarty_Internal_Template $template) {

    $name   = isset($params['name']) ? trim($params['name']) : '';
    $content = isset($params['content']) ? trim(str_replace(["\n", "\r"], " ", $params['content'])) : '';

    if (!empty($name)) {
        EtdOptimizer::addMeta($name, $content);
    }

}
