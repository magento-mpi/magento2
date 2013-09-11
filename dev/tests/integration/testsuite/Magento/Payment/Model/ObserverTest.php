<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Payment_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Event_Observer
     */
    protected $_eventObserver;

    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_eventObserver = $this->_createEventObserver();
    }

    /**
     * Check that Magento_Payment_Model_Observer::updateOrderStatusForPaymentMethods()
     * is called as event and it can change status
     *
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Payment/_files/order_status.php
     */
    public function testUpdateOrderStatusForPaymentMethodsEvent()
    {
        $statusCode = 'custom_new_status';
        $data = array(
            'section' => 'payment',
            'website' => 1,
            'store' => 1,
            'groups' => array(
                'checkmo' => array(
                    'fields' => array(
                        'order_status' => array(
                            'value' => $statusCode
                        )
                    )
                )
            )
        );
        $this->_objectManager->create('Magento_Backend_Model_Config')
            ->setSection('payment')
            ->setWebsite('base')
            ->setGroups(array('groups' => $data['groups']))
            ->save();

        /** @var Magento_Sales_Model_Order_Status $status */
        $status = $this->_objectManager->get('Magento_Sales_Model_Order_Status')->load($statusCode);

        /** @var $storeConfig Magento_Core_Model_Store_Config */
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Store_Config');
        $defaultStatus = (string)$storeConfig->getConfig('payment/checkmo/order_status');

        /** @var Magento_Core_Model_Resource_Config $config */
        $config = $this->_objectManager->get('Magento_Core_Model_Resource_Config');
        $config->saveConfig('payment/checkmo/order_status', $statusCode, 'default', 0);

        $this->_resetConfig();

        $newStatus = $storeConfig->getConfig('payment/checkmo/order_status');

        $status->unassignState(Magento_Sales_Model_Order::STATE_NEW);

        $this->_resetConfig();

        $unassignedStatus = $storeConfig->getConfig('payment/checkmo/order_status');

        $this->assertEquals('pending', $defaultStatus);
        $this->assertEquals($statusCode, $newStatus);
        $this->assertEquals('pending', $unassignedStatus);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testUpdateOrderStatusForPaymentMethods()
    {
        $statusCode = 'custom_new_status';

        /** @var Magento_Core_Model_Resource_Config $config */
        $config = $this->_objectManager->get('Magento_Core_Model_Resource_Config');
        $config->saveConfig('payment/checkmo/order_status', $statusCode, 'default', 0);

        $this->_resetConfig();

        $observer = $this->_objectManager->create('Magento_Payment_Model_Observer');
        $observer->updateOrderStatusForPaymentMethods($this->_eventObserver);

        $this->_resetConfig();

        /** @var $storeConfig Magento_Core_Model_Store_Config */
        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Store_Config');
        $unassignedStatus = $storeConfig->getConfig('payment/checkmo/order_status');
        $this->assertEquals('pending', $unassignedStatus);
    }

    /**
     * Create event observer
     *
     * @return Magento_Event_Observer
     */
    protected function _createEventObserver()
    {
        $data = array('status' => 'custom_new_status', 'state' => Magento_Sales_Model_Order::STATE_NEW);
        $event = $this->_objectManager->create('Magento_Event', array('data' => $data));
        return $this->_objectManager->create('Magento_Event_Observer', array('data' => array('event' => $event)));
    }

    /**
     * Clear config cache
     */
    protected function _resetConfig()
    {
        /** @var $configModel Magento_Core_Model_Config */
        $configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
        $configModel->reinit();
        $this->_objectManager->create('Magento_Core_Model_StoreManagerInterface')->reinitStores();
    }
}
