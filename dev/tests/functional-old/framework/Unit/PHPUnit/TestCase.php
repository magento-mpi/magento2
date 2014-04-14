<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Unit_PHPUnit_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig;

    /**
     * Class constructor
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_testConfig = Mage_Selenium_TestConfiguration::getInstance();
    }
}