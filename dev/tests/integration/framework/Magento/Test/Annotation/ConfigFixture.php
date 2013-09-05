<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Implementation of the @magentoConfigFixture DocBlock annotation
 */
class Magento_Test_Annotation_ConfigFixture
{
    /**
     * Test instance that is available between 'startTest' and 'stopTest' events
     *
     * @var PHPUnit_Framework_TestCase
     */
    protected $_currentTest;

    /**
     * Original values for global configuration options that need to be restored
     *
     * @var array
     */
    private $_globalConfigValues = array();

    /**
     * Original values for store-scoped configuration options that need to be restored
     *
     * @var array
     */
    private $_storeConfigValues = array();

    /**
     * Retrieve configuration node value
     *
     * @param string $configPath
     * @param string|bool|null $storeCode
     * @return string
     */
    protected function _getConfigValue($configPath, $storeCode = false)
    {
        if ($storeCode === false) {
            $result = Mage::getConfig()->getNode($configPath);
        } else {
            $result = Mage::getStoreConfig($configPath, $storeCode);
        }
        if ($result instanceof SimpleXMLElement) {
            $result = (string)$result;
        }
        return $result;
    }

    /**
     * Assign configuration node value
     *
     * @param string $configPath
     * @param string $value
     * @param string|bool|null $storeCode
     */
    protected function _setConfigValue($configPath, $value, $storeCode = false)
    {
        if ($storeCode === false) {
            Mage::getConfig()->setNode($configPath, $value);
            $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
            $objectManager->get('Magento_Core_Model_Config_Modules')->setNode($configPath, $value);
            $objectManager->get('Magento_Core_Model_Config_Primary')->setNode($configPath, $value);
            $objectManager->get('Magento_Core_Model_Config_Locales')->setNode($configPath, $value);
        } else {
            Mage::app()->getStore($storeCode)->setConfig($configPath, $value);
        }
    }

    /**
     * Assign required config values and save original ones
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    protected function _assignConfigData(PHPUnit_Framework_TestCase $test)
    {
        $annotations = $test->getAnnotations();
        if (!isset($annotations['method']['magentoConfigFixture'])) {
            return;
        }
        foreach ($annotations['method']['magentoConfigFixture'] as $configPathAndValue) {
            if (preg_match('/^.+?(?=_store\s)/', $configPathAndValue, $matches)) {
                /* Store-scoped config value */
                $storeCode = ($matches[0] != 'current' ? $matches[0] : '');
                list(, $configPath, $requiredValue) = preg_split('/\s+/', $configPathAndValue, 3);

                $originalValue = $this->_getConfigValue($configPath, $storeCode);
                $this->_storeConfigValues[$storeCode][$configPath] = $originalValue;

                $this->_setConfigValue($configPath, $requiredValue, $storeCode);
            } else {
                /* Global config value */
                list($configPath, $requiredValue) = preg_split('/\s+/', $configPathAndValue, 2);

                $originalValue = $this->_getConfigValue($configPath);
                $this->_globalConfigValues[$configPath] = $originalValue;

                $this->_setConfigValue($configPath, $requiredValue);
            }

        }
    }

    /**
     * Restore original values for changed config options
     */
    protected function _restoreConfigData()
    {
        /* Restore global values */
        foreach ($this->_globalConfigValues as $configPath => $originalValue) {
            $this->_setConfigValue($configPath, $originalValue);
        }
        $this->_globalConfigValues = array();

        /* Restore store-scoped values */
        foreach ($this->_storeConfigValues as $storeCode => $originalData) {
            foreach ($originalData as $configPath => $originalValue) {
                $this->_setConfigValue($configPath, $originalValue, $storeCode);
            }
        }
        $this->_storeConfigValues = array();
    }

    /**
     * Handler for 'startTest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     */
    public function startTest(PHPUnit_Framework_TestCase $test)
    {
        $this->_currentTest = $test;
        $this->_assignConfigData($test);
    }

    /**
     * Handler for 'endTest' event
     *
     * @param PHPUnit_Framework_TestCase $test
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(PHPUnit_Framework_TestCase $test)
    {
        $this->_currentTest = null;
        $this->_restoreConfigData();
    }

    /**
     * Reassign configuration data whenever application is reset
     */
    public function initStoreAfter()
    {
        /* process events triggered from within a test only */
        if ($this->_currentTest) {
            $this->_assignConfigData($this->_currentTest);
        }
    }
}
