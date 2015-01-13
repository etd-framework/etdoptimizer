<?php
/**
 * @package      ETD Optimizer
 *
 * @version      2.0
 * @copyright    Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

include('vendor/autoload.php');

/*
 * On détermine le bon wrapper.
 */
$wrapper = '';

// Joomla
// ------------

if (defined('JEXEC')) {
    $wrapper = 'joomla';
}

// Prestashop
// ------------

elseif (defined('_CAN_LOAD_FILES_')) {
    $wrapper = 'prestashop';
}

/*
 * On charge le wrapper si nécessaire.
 */

if (!empty($wrapper)) {

    $path = realpath(dirname(__FILE__) . "/platforms/" . $wrapper . ".php");
    if (file_exists($path)) {
        require_once($path);
    }

}