<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Core\Model\LocaleInterface;
use Magento\Customer\Model\Customer as CustomerModel;

/**
 * Magento\Customer\Block\Adminhtml\Edit\Tab\View
 *
 * @magentoAppArea adminhtml
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    const CURRENT_CUSTOMER = 'current_customer';

    /** @var  \Magento\Backend\Block\Template\Context */
    private $_context;

    /** @var  \Magento\Core\Model\Registry */
    private $_coreRegistry;

    /** @var  \Magento\Customer\Model\CustomerFactory */
    private $_customerFactory;

    /** @var  \Magento\Customer\Service\V1\CustomerGroupServiceInterface */
    private $_groupService;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    private $_storeManager;

    /** @var  View */
    private $_block;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_storeManager = $objectManager->get('Magento\Core\Model\StoreManager');
        $this->_context = $objectManager
            ->get(
                'Magento\Backend\Block\Template\Context',
                array('storeManager' => $this->_storeManager)
            );

        $this->_customerFactory = $objectManager->get('Magento\Customer\Model\CustomerFactory');
        $this->_coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $this->_groupService = $objectManager->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $this->_block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\View',
                '',
                array(
                    'context' => $this->_context,
                    'groupService' => $this->_groupService,
                    'registry' => $this->_coreRegistry
                )
            );
    }

    public function tearDown()
    {
        $this->_coreRegistry->unregister(self::CURRENT_CUSTOMER);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomer()
    {
        $this->assertSame($this->_loadCustomer(), $this->_block->getCustomer());
    }

    public function testGetCustomerEmpty()
    {
        $this->assertSame($this->_createCustomer(), $this->_block->getCustomer());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetGroupName()
    {
        $groupName = $this->_groupService->getGroup($this->_loadCustomer()->getGroupId())->getCode();
        $this->assertEquals($groupName, $this->_block->getGroupName());
    }

    public function testGetGroupNameNull()
    {
        $this->_createCustomer();
        $this->assertNull($this->_block->getGroupName());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCreateDate()
    {
        $createdAt = $this->_block
            ->formatDate($this->_loadCustomer()->getCreatedAt(), LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        $this->assertEquals($createdAt, $this->_block->getCreateDate());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetStoreCreateDate()
    {
        $customer = $this->_loadCustomer();
        $date = $this->_context
            ->getLocale()->storeDate($customer->getStoreId(), $customer->getCreatedAtTimestamp(), true);
        $storeCreateDate = $this->_block->formatDate($date, LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        $this->assertEquals($storeCreateDate, $this->_block->getStoreCreateDate());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetStoreCreateDateTimezone()
    {
        $timezone = $this->_context
            ->getStoreConfig()
            ->getConfig(LocaleInterface::XML_PATH_DEFAULT_TIMEZONE, $this->_loadCustomer()->getStoreId());
        $this->assertEquals($timezone, $this->_block->getStoreCreateDateTimezone());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsConfirmedStatusConfirmed()
    {
        $this->_loadCustomer()->setConfirmation(false);
        $this->assertEquals('Confirmed', $this->_block->getIsConfirmedStatus());
    }

    /**
     * @magentoConfigFixture default_store customer/create_account/confirm 1
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsConfirmedStatusConfirmationIsRequired()
    {
        $this->_loadCustomer()->setConfirmation(true);
        $this->assertEquals('Not confirmed, cannot login', $this->_block->getIsConfirmedStatus());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsConfirmedStatusConfirmationIsNotRequired()
    {
        $customer = $this->_loadCustomer();
        $customer->setConfirmation(true);
        $customer->setSkipConfirmationIfEmail($customer->getEmail());
        $this->assertEquals('Not confirmed, can login', $this->_block->getIsConfirmedStatus());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCreatedInStore()
    {
        $storeName = $this->_storeManager->getStore($this->_loadCustomer()->getStoreId())->getName();
        $this->assertEquals($storeName, $this->_block->getCreatedInStore());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetStoreId()
    {
        $this->assertEquals($this->_loadCustomer()->getStoreId(), $this->_block->getStoreId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetBillingAddressHtml()
    {
        $html = $this->_loadCustomer()->getPrimaryBillingAddress()->format('html');
        $this->assertEquals($html, $this->_block->getBillingAddressHtml());
    }

    public function testGetBillingAddressHtmlNoDefaultAddress()
    {
        $this->_createCustomer();
        $this->assertEquals(
            __('The customer does not have default billing address.'), $this->_block->getBillingAddressHtml()
        );
    }

    public function testGetTabLabel()
    {
        $this->assertEquals(__('Customer View'), $this->_block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals(__('Customer View'), $this->_block->getTabTitle());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCanShowTab()
    {
        $this->_loadCustomer();
        $this->assertTrue($this->_block->canShowTab());
    }

    public function testCanShowTabNot()
    {
        $this->_createCustomer();
        $this->assertFalse($this->_block->canShowTab());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testIsHidden()
    {
        $this->_loadCustomer();
        $this->assertFalse($this->_block->isHidden());
    }

    public function testIsHiddenNot()
    {
        $this->_createCustomer();
        $this->assertTrue($this->_block->isHidden());
    }

    /**
     * @return CustomerModel
     */
    private function _createCustomer()
    {
        $customer = $this->_customerFactory->create();
        $this->_coreRegistry->register(self::CURRENT_CUSTOMER, $customer);
        return $customer;
    }

    /**
     * @return CustomerModel
     */
    private function _loadCustomer()
    {
        return $this->_createCustomer()->load(1);
    }
}
