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
 * @param int $id_cms_category L'identifiant de la catégorie
 */
function smarty_modifier_getCMSCategory($id_cms_category) {

	/**
	 * @var array $cms_categories Cache des catégories
	 */
	static $cms_categories = [];

	// On s'assure d'avoir un integer.
    $id_cms_category = (int) $id_cms_category;

	if (isset($cms_categories[$id_cms_category])) {
		return $cms_categories[$id_cms_category];
	}

	// On récupère le contexte.
	$context = Context::getContext();

	// On récupère la catégorie
	$category = new CMSCategory($id_cms_category, $context->language->id, $context->shop->id);

	if (isset($category->id)) {
        $cms_categories[$id_cms_category] = $category;
		return $category;
	}

	return false;

}
