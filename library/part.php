<?php
/**
 * @package		Etudoo - ETD Solutions
 * @subpackage	Plugin ETDOptimizer
 * 
 * @version		1.1.1
 * @copyright	Copyright (C) 2012 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license		http://www.etd-solutions.com/licence
 * @author		ETD Solutions http://www.etd-solutions.com
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
