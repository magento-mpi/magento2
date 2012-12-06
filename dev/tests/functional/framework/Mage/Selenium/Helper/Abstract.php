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
 * Abstract helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Abstract
{
    /**
     * Test configuration object
     * @var Mage_Selenium_TestConfiguration|null
     */
    protected $_config = null;

    /**
     * Constructor expects global test configuration object
     *
     * @param Mage_Selenium_TestConfiguration $config
     */
    public function  __construct(Mage_Selenium_TestConfiguration $config)
    {
        $this->_config = $config;
        $this->_init();
    }

    /**
     * @return Mage_Selenium_Helper_Abstract
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
}
