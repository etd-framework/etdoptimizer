<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.7.0
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

/**
 * @param int $id_cms L'identifiant de la catégorie
 */
function smarty_modifier_getCMS($id_cms) {

    static $array = [];

    // On s'assure d'avoir un integer.
    $id_cms = (int) $id_cms;

    if (isset($array[$id_cms])) {
        return $array[$id_cms];
    }

    // On récupère le contexte.
    $context = Context::getContext();

    // On récupère le bloc CMS
    $cms = new CMS($id_cms, $context->language->id, $context->shop->id);

    if (isset($cms->id)) {
        $array[$id_cms] = $cms;
        return $cms;
    }

    return false;

}
