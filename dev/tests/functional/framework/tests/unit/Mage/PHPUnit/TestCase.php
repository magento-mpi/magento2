<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Test Case
 */
abstract class Mage_PHPUnit_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Selenium Test Configuration instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_config = null;

    /**
     * Class constructor
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_config = Mage_Selenium_TestConfiguration::getInstance();
    }
}