<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Model\Observer\Session
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession;

    /**
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookieMock;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_persistentSession = $this->_objectManager->get('Magento\Persistent\Helper\Session');
        $this->_cookieMock = $this->getMock('Magento\Stdlib\Cookie', array('set'), array(), '', false);
        $this->_customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $this->_model = $this->_objectManager->create(
            'Magento\Persistent\Model\Observer\Session',
            array(
                'persistentSession' => $this->_persistentSession,
                'cookie' => $this->_cookieMock,
                'customerSession' => $this->_customerSession
            )
        );
    }

    /**
     * @covers \Magento\Persistent\Model\Observer\Session::synchronizePersistentOnLogin
     */
    public function testSynchronizePersistentOnLogin()
    {
        $event = new \Magento\Event();
        $observer = new \Magento\Event\Observer(array('event' => $event));

        /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService */
        $customerAccountService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        /** @var $customer \Magento\Customer\Service\V1\Data\Customer */
        $customer = $customerAccountService->getCustomer(1);
        $event->setData('customer', $customer);
        $this->_persistentSession->setRememberMeChecked(true);
        $this->_cookieMock->expects(
            $this->once()
        )->method(
            'set'
        )->with(
            \Magento\Persistent\Model\Session::COOKIE_NAME,
            $this->anything(),
            $this->anything(),
            $this->_customerSession->getCookiePath()
        );
        $this->_model->synchronizePersistentOnLogin($observer);

        // check that persistent session has been stored for Customer
        /** @var \Magento\Persistent\Model\Session $sessionModel */
        $sessionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Model\Session'
        );
        $sessionModel->loadByCustomerId(1);
        $this->assertEquals(1, $sessionModel->getCustomerId());
    }

    /**
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 1
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     */
    public function testSynchronizePersistentOnLogout()
    {
        $this->_customerSession->loginById(1);

        // check that persistent session has been stored for Customer
        /** @var \Magento\Persistent\Model\Session $sessionModel */
        $sessionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Model\Session'
        );
        $sessionModel->loadByCookieKey();
        $this->assertEquals(1, $sessionModel->getCustomerId());

        $this->_customerSession->logout();

        /** @var \Magento\Persistent\Model\Session $sessionModel */
        $sessionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Persistent\Model\Session'
        );
        $sessionModel->loadByCookieKey();
        $this->assertNull($sessionModel->getCustomerId());
    }
}
