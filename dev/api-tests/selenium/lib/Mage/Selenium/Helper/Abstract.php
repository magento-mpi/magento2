<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Helper_Abstract
{
    /**
     * Current test case
     * @var Mage_Selenium_TestCase
     */
    protected $_testCase = null;

    /**
     * Test configuration object
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_config = null;

    /**
     * Constructor, expects global test configuration object
     *
     * @param Mage_Selenium_TestConfiguration $config Test configuration
     */
    public function  __construct(Mage_Selenium_TestConfiguration $config)
    {
        $this->_config = $config;
        $this->_init();
    }

    /**
     * Initialize object
     * @return Mage_Selenium_AbstractHelper
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Return config
     * @return Mage_Selenium_TestConfiguration|null
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set current testcase object to allow callbacks
     *
     * @param Mage_Selenium_TestCase $testCase Current test case
     *
     * @return Mage_Selenium_Helper_Abstract
     */
    public function setTestCase(Mage_Selenium_TestCase $testCase)
    {
        $this->_testCase = $testCase;
        return $this;
    }
}
