<?php
/**
 * Verifies that the card types defined in payment xml matches the types declared in factory via DI.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Centinel_StateFactoryTest extends PHPUnit_Framework_TestCase
{

    public function testFactoryTypes()
    {
        $factoryTypes = $this->_getFactoryTypes();
        $ccTypes = $this->_getCcTypes();

        $definedTypes = array_intersect($factoryTypes, $ccTypes);

        $this->assertEquals($factoryTypes, $definedTypes, 'Some factory types are missing from payments config.'
            . "\nMissing types: " . implode(',', array_diff($factoryTypes, $definedTypes)));
    }

    /**
     * Get factory, find list of types it has
     *
     * @return array string[] factoryTypes
     */
    private function _getFactoryTypes()
    {
        /** @var Magento_Centinel_Model_StateFactory $stateFactory */
        $stateFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Centinel_Model_StateFactory');
        $reflectionObj = new ReflectionClass($stateFactory);
        $stateMapProp = $reflectionObj->getProperty('_stateClassMap');
        $stateMapProp->setAccessible(true);
        $stateClassMap = $stateMapProp->getValue($stateFactory);
        $factoryTypes = array_keys($stateClassMap);
        return $factoryTypes;
    }

    /**
     * Get config, find list of types it has
     *
     * @return array string[] ccTypes
     */
    private function _getCcTypes()
    {
        /** @var Magento_Payment_Model_Config $paymentConfig */
        $paymentConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Payment_Model_Config');
        $ccTypes = array_keys($paymentConfig->getCcTypes());
        return $ccTypes;
    }
}
