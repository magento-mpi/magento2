<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PersistentHistory\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\PersistentHistory\Model\Observer
     */
    protected $_observerModel;

    /**
     * @var \Magento\Event
     */
    protected $_event;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData;

    /**
     * @var \Magento\Persistent\Model\SessionFactory
     */
    protected $_sessionFactory;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_event = new \Magento\Event();
        $this->_observer = new \Magento\Event\Observer();
        $this->_observer->setEvent( $this->_event);

        $this->_customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        $this->_persistentSessionHelper = $this->_objectManager->create('Magento\Persistent\Helper\Session');
        $this->_wishlistData = $this->_objectManager->create('Magento\Wishlist\Helper\Data');
        $this->_observerModel = $this->_objectManager->create(
            'Magento\PersistentHistory\Model\Observer',
            [
                'customerSession' => $this->_customerSession,
                'persistentSession' => $this->_persistentSessionHelper,
                'wishlistData' => $this->_wishlistData
            ]
        );

        $this->_sessionFactory = $this->_objectManager->create('Magento\Persistent\Model\SessionFactory');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture current_store persistent/options/customer 1
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/wishlist 1
     */
    public function testEmulateCustomerWishlist()
    {
        /** @var \Magento\Persistent\Model\Session $sessionModel */
        $sessionModel = $this->_sessionFactory->create();
        $sessionModel->setCustomerId(1)->save();

        $this->_persistentSessionHelper->setSession($sessionModel);

        $this->_observerModel->emulateCustomer($this->_observer);
        $this->assertEquals($this->_customerSession->getCustomerDataObject(), $this->_wishlistData->getCustomer());
    }
}
