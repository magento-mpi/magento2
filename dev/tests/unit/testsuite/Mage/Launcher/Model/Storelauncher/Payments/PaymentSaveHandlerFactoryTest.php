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

class Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory
     */
    protected $_saveHandlerFactory;

    protected function setUp()
    {
        // Mock payment save handler factory
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_saveHandlerFactory = new Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory(
            $objectManager
        );
    }

    protected function tearDown()
    {
        $this->_saveHandlerFactory = null;
    }

    public function testGetPaymentSaveHandlerMapContainsValidPaymentSaveHandlers()
    {
        foreach ($this->_saveHandlerFactory->getPaymentSaveHandlerMap() as $saveHandlerClassName) {
            $this->assertTrue(class_exists($saveHandlerClassName));
            $saveHandlerClass = new ReflectionClass($saveHandlerClassName);
            $this->assertTrue(
                $saveHandlerClass->isSubclassOf('Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandler')
            );
        }
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
        $saveHandlerMap = $this->_saveHandlerFactory->getPaymentSaveHandlerMap();
        foreach ($tileSaveHandler->getRelatedPaymentMethods() as $paymentId) {
            $this->assertArrayHasKey($paymentId, $saveHandlerMap,
                'There is no save handler for payment method with code "' . $paymentId. '".');
        }
    }
}
