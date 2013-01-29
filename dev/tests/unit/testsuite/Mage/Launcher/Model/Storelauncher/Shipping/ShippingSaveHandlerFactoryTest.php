<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactoryTest
    extends Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerFactory_TestCaseAbstract
{
    /**
     * @param Magento_ObjectManager $objectManager
     * @return Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerFactoryAbstract
     */
    public function getSaveHandlerFactoryInstance(Magento_ObjectManager $objectManager)
    {
        return new Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory($objectManager);
    }

    public function testGetSaveHandlerMapContainsSaveHandlersForAllShippingMethodsRelatedToShippingTile()
    {
        /**
         * @var $tileSaveHandler Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler
         */
        $tileSaveHandler = $this->getMock('Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler', null, array(),
            '', false);
        $saveHandlerMap = $this->_saveHandlerFactory->getSaveHandlerMap();
        foreach ($tileSaveHandler->getRelatedShippingMethods() as $shippingId) {
            $this->assertArrayHasKey($shippingId, $saveHandlerMap,
                'There is no save handler for shipping method with code "' . $shippingId. '".');
        }
    }
}
