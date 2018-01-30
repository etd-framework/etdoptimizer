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
 * @param int $id_category L'identifiant de la catégorie
 */
function smarty_modifier_getProductCategory($id_category) {

	/**
	 * @var array $categories Cache des catégories
	 */
	static $categories = [];

	// On s'assure d'avoir un integer.
	$id_category = (int) $id_category;

	if (isset($categories[$id_category])) {
		return $categories[$id_category];
	}

	// On récupère le contexte.
	$context = Context::getContext();

	// On récupère la catégorie
	$category = new Category($id_category, $context->language->id, $context->shop->id);

	if (isset($category->id)) {
		$categories[$id_category] = $category;
		return $category;
	}

	return false;

}
