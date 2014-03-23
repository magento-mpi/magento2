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

namespace Magento\Payment\Model;

/**
 * @magentoAppArea adminhtml
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_eventObserver;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_eventObserver = $this->_createEventObserver();
    }

    /**
     * Check that \Magento\Payment\Model\Observer::updateOrderStatusForPaymentMethods()
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
        $this->_objectManager->create('Magento\Backend\Model\Config')
            ->setSection('payment')
            ->setWebsite('base')
            ->setGroups(array('groups' => $data['groups']))
            ->save();

        /** @var \Magento\Sales\Model\Order\Status $status */
        $status = $this->_objectManager->get('Magento\Sales\Model\Order\Status')->load($statusCode);

        /** @var $scopeConfig \Magento\App\Config\ScopeConfigInterface */
        $scopeConfig = $this->_objectManager->get('Magento\App\Config\ScopeConfigInterface');
        $defaultStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        /** @var \Magento\Core\Model\Resource\Config $config */
        $config = $this->_objectManager->get('Magento\Core\Model\Resource\Config');
        $config->saveConfig('payment/checkmo/order_status', $statusCode, \Magento\BaseScopeInterface::SCOPE_DEFAULT, 0);

        $this->_resetConfig();

        $newStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $status->unassignState(\Magento\Sales\Model\Order::STATE_NEW);

        $this->_resetConfig();

        $unassignedStatus = $scopeConfig->getConfig('payment/checkmo/order_status');

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

        /** @var \Magento\Core\Model\Resource\Config $config */
        $config = $this->_objectManager->get('Magento\Core\Model\Resource\Config');
        $config->saveConfig('payment/checkmo/order_status', $statusCode, 'default', 0);

        $this->_resetConfig();

        $observer = $this->_objectManager->create('Magento\Payment\Model\Observer');
        $observer->updateOrderStatusForPaymentMethods($this->_eventObserver);

        $this->_resetConfig();

        /** @var $scopeConfig \Magento\App\Config\ScopeConfigInterface */
        $scopeConfig = $this->_objectManager->get('Magento\App\Config\ScopeConfigInterface');
        $unassignedStatus = (string)$scopeConfig->getValue(
            'payment/checkmo/order_status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->assertEquals('pending', $unassignedStatus);
    }

    /**
     * Create event observer
     *
     * @return \Magento\Event\Observer
     */
    protected function _createEventObserver()
    {
        $data = array('status' => 'custom_new_status', 'state' => \Magento\Sales\Model\Order::STATE_NEW);
        $event = $this->_objectManager->create('Magento\Event', array('data' => $data));
        return $this->_objectManager->create('Magento\Event\Observer', array('data' => array('event' => $event)));
    }

    /**
     * Clear config cache
     */
    protected function _resetConfig()
    {
        $this->_objectManager->get('Magento\App\Config\ReinitableConfigInterface')->reinit();
        $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->reinitStores();
    }
}
