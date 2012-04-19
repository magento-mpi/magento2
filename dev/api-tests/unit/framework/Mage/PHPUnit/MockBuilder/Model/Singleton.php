<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class which creates mock object for models when they are created
 * in a code using Mage::getSingleton('...');
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_Singleton extends Mage_PHPUnit_MockBuilder_Model_Abstract
{
    /**
     * Singleton key prefix needed for Mage::registry
     *
     * @var string
     */
    protected $_regisrtyKeyPrefix = '_singleton';

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_Model
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_model');
    }

    /**
     * Returns PHPUnit singleton helper.
     *
     * @return Mage_PHPUnit_Helper_Singleton
     */
    protected function _getSingletonHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('singleton');
    }

    /**
     * Method which is called after getMock() method.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _afterGetMock($mock)
    {
        $this->_addMockToRegistry($mock);
    }

    /**
     * Returns full registry key.
     *
     * @param string $modelKey
     * @return string
     */
    protected function _getRegisrtyKey($modelKey)
    {
        return $this->_regisrtyKeyPrefix . '/' . $modelKey;
    }

    /**
     * Adds mock object to registry.
     *
     * @param PHPUnit_Framework_MockObject_MockObject|object $mock
     */
    protected function _addMockToRegistry($mock)
    {
        $registryKey = $this->_getRegisrtyKey($this->getModel());

        $this->_getSingletonHelper()->registerSingleton($registryKey, $mock);
    }
}
