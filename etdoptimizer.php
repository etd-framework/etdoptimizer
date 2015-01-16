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
 * On détermine la bonne plateforme.
 */
$platform = '';

// Joomla
// ------------

if (defined('_JEXEC')) {
    $platform = 'joomla';
}

// Prestashop
// ------------

elseif (defined('_CAN_LOAD_FILES_')) {
    $platform = 'prestashop';
}

/*
 * On charge le plugin si nécessaire.
 */

if (!empty($platform)) {

    $path = realpath(dirname(__FILE__) . "/platforms/" . $platform . "/plugin.php");
    if (file_exists($path)) {
        require_once($path);
    }

}