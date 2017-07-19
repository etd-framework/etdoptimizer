<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

function smarty_function_addCustom($params, Smarty_Internal_Template $template) {

    $html = isset($params['html']) ? trim($params['html']) : '';

    if (!empty($html)) {
        EtdOptimizer::addCustom($html);
    }

}
