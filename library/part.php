<?php

/**
 * @package      ETD Optimizer
 *
 * @version      2.6.5
 * @copyright    Copyright (C) 2012-2017 ETD Solutions. Tous droits réservés.
 * @license      Apache Version 2 (https://raw.githubusercontent.com/jbanety/etdoptimizer/master/LICENSE.md)
 * @author       ETD Solutions http://www.etd-solutions.com
 **/

abstract class EtdOptimizerPart {

	/**
	 * @var EtdOptimizerHelper
	 */
	protected $helper;

	public function __construct($helper) {
		$this->helper = $helper;
	}
	
	abstract public function render();
	
}
