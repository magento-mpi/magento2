<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Customer\Service\V1\Dto\Address;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/Customer/_files/customer_sample.php
 */
class AddressesTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerServiceInterface */
    private $_customerService;

    /** @var CustomerAddressServiceInterface */
    private $_addressService;

    /** @var  \Magento\Registry */
    private $_coreRegistry;
    /** @var \Magento\Backend\Model\Session */
    private $_backendSession;

    /** @var  \Magento\ObjectManager */
    private $_objectManager;

    /** @var  array */
    private $_customerData;

    public function setUp()
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_customerService = $this->_objectManager->get(
            'Magento\Customer\Service\V1\CustomerServiceInterface'
        );
        $this->_addressService = $this->_objectManager->get(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );
        $this->_coreRegistry = $this->_objectManager->get('Magento\Registry');
        $this->_backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');

        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);
    }

    public function tearDown()
    {
        $this->_backendSession->unsCustomerData();
        $this->_coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Validate country default gets displayed
     */
    public function testInitFormEmpty()
    {
        $block = $this->_objectManager->create('Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses');
        $this->_backendSession->setCustomerData(['account' => [], 'address' => []]);

        /** @var Addresses $block */
        $block = $block->initForm();
        /** @var \Magento\Data\Form $form */
        $form = $block->getForm();

        // Validate Country gets set
        $this->assertEquals('US', $form->getElement('country_id')->getValue());
    }

    public function testInitForm()
    {
        $this->setupExistingCustomerData();
        $block = $this->_objectManager->create('Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses');

        /** @var Addresses $block */
        $block = $block->initForm();
        /** @var \Magento\Data\Form $form */
        $form = $block->getForm();

        $this->assertInstanceOf('Magento\Data\Form\Element\Fieldset', $form->getElement('address_fieldset'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('prefix'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('firstname'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('middlename'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('lastname'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('suffix'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('company'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Multiline', $form->getElement('street'));
        $this->assertEquals(2, $form->getElement('street')->getLineCount());
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('city'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Select', $form->getElement('country_id'));
        $this->assertEquals('US', $form->getElement('country_id')->getValue());
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('region'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Hidden', $form->getElement('region_id'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('postcode'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('telephone'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('fax'));
        $this->assertInstanceOf('Magento\Data\Form\Element\Text', $form->getElement('vat_id'));
    }

    public function testToHtml()
    {
        $this->setupExistingCustomerData();
        /** @var \Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses $block */
        $block = $this->_objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses');

        $html = $block->initForm()->toHtml();

        $this->assertContains('Customer Addresses', $html);
        $this->assertContains('Remove address', $html);
        $this->assertContains('Edit address', $html);
        $this->assertContains('test firstname test lastname', $html);
        $this->assertContains('test street', $html);
        $this->assertContains('removed street', $html);
        $this->assertContains('T: +7000000001', $html);
        $this->assertContains('Default Billing Address', $html);
        $this->assertContains('Default Shipping Address', $html);
        $this->assertContains('Add New Address', $html);
        $this->assertContains('<option value="US" selected="selected">United States</option>', $html);
        $this->assertContains('Texas', $html);

        $this->assertContains('<li class="address-list-item" id="address_item_1" data-item="1">', $html);
        $this->assertContains('<a href="#form_address_item_3"', $html);
        $this->assertContains('<div class="address-item-edit-content"', $html);
        $this->assertContains('id="form_address_item_1" data-item="1"', $html);
        $this->assertContains('{"name": "address_item_1"}}', $html);
        $this->assertContains('<input id="_item1prefix" name="address[1][prefix]"', $html);
    }

    /**
     * Put existing customer data into the backend session
     */
    protected function setupExistingCustomerData()
    {
        /** @var Customer $customer */
        $customer = $this->_customerService->getCustomer(1);
        $this->_customerData = [
            'customer_id' => $customer->getCustomerId(),
            'account' => $customer->getAttributes(),
        ];
        $this->_customerData['account']['id'] = $customer->getCustomerId();
        /** @var Address[] $addresses */
        $addresses = $this->_addressService->getAddresses(1);
        foreach ($addresses as $addressDto) {
            $this->_customerData['address'][$addressDto->getId()] = $addressDto->getAttributes();
            $this->_customerData['address'][$addressDto->getId()]['id'] = $addressDto->getId();
        }
        $this->_backendSession->setCustomerData($this->_customerData);
    }
}
