<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactoryTest
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactory_TestCaseAbstract
{
    /**
     * @param Magento_ObjectManager $objectManager
     * @return Saas_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerFactoryAbstract
     */
    public function getSaveHandlerFactoryInstance(Magento_ObjectManager $objectManager)
    {
        return new Saas_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory($objectManager);
    }

    public function testGetSaveHandlerMapContainsSaveHandlersForAllShippingMethodsRelatedToShippingTile()
    {
        /**
         * @var $tileSaveHandler Saas_Launcher_Model_Storelauncher_Shipping_SaveHandler
         */
        $tileSaveHandler = $this->getMock('Saas_Launcher_Model_Storelauncher_Shipping_SaveHandler', null, array(),
            '', false);
        $saveHandlerMap = $this->_saveHandlerFactory->getSaveHandlerMap();
        foreach ($tileSaveHandler->getRelatedShippingMethods() as $shippingId) {
            $this->assertArrayHasKey($shippingId, $saveHandlerMap,
                'There is no save handler for shipping method with code "' . $shippingId. '".');
        }
    }
}
