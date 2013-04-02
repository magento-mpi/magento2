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

class Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactoryTest
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerFactory_TestCaseAbstract
{
    /**
     * @param Magento_ObjectManager $objectManager
     * @return Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerFactoryAbstract
     */
    public function getSaveHandlerFactoryInstance(Magento_ObjectManager $objectManager)
    {
        return new Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory($objectManager);
    }

    public function testGetPaymentSaveHandlerMapContainsSaveHandlersForAllPaymentMethodsRelatedToPaymentsTile()
    {
        /**
         * @var $tileSaveHandler Mage_Launcher_Model_Storelauncher_Payments_SaveHandler
         */
        $tileSaveHandler = $this->getMock('Mage_Launcher_Model_Storelauncher_Payments_SaveHandler',
            null, // do not mock any method
            array(),
            '',
            false);
        $saveHandlerMap = $this->_saveHandlerFactory->getSaveHandlerMap();
        foreach ($tileSaveHandler->getRelatedPaymentMethods() as $paymentId) {
            $this->assertArrayHasKey($paymentId, $saveHandlerMap,
                'There is no save handler for payment method with code "' . $paymentId. '".');
        }
    }
}
