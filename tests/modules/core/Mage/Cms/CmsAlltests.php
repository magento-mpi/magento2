<?php

require_once 'lib/PHPUnit/Framework/TestSuite.php';

/**
 * Static test suite.
 */
class CmsAlltests extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'CmsAlltests' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

