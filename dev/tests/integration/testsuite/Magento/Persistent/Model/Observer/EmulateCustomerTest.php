<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 */
class EmulateCustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Persistent\Model\Observer\EmulateCustomer
     */
    protected $_observer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');

        $this->_customerAccountService = $this->_objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        $this->_persistentSessionHelper = $this->_objectManager->create('Magento\Persistent\Helper\Session');

        $this->_observer = $this->_objectManager->create(
            'Magento\Persistent\Model\Observer\EmulateCustomer',
            [
                'customerAccountService' => $this->_customerAccountService,
                'persistentSession' => $this->_persistentSessionHelper
            ]
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     * @magentoConfigFixture current_store persistent/options/enabled 1
     */
    public function testEmulateCustomer()
    {
        $observer = new \Magento\Framework\Event\Observer();

        $this->_customerSession->loginById(1);
        $this->_customerSession->logout();
        $this->assertNull($this->_customerSession->getCustomerId());
        $this->assertEquals(
            \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
            $this->_customerSession->getCustomerGroupId()
        );

        $this->_observer->execute($observer);
        $customer = $this->_customerAccountService->getCustomer(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->assertEquals(
            $customer->getId(),
            $this->_customerSession->getCustomerId()
        );
        $this->assertEquals(
            $customer->getGroupId(),
            $this->_customerSession->getCustomerGroupId()
        );
    }
}
