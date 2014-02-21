<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Test Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/Customer/_files/customer_sample.php
 */
class AddressesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    private $_customerService;

    /** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface */
    private $_addressService;

    /** @var  \Magento\Core\Model\Registry */
    private $_coreRegistry;

    /** @var  \Magento\Backend\Block\Template\Context */
    private $_context;

    /** @var \Magento\Backend\Model\Session */
    private $_backendSession;

    /** @var  \Magento\Customer\Service\V1\Dto\AddressBuilder */
    private $_addressBuilder;

    /** @var  \Magento\Customer\Service\V1\Dto\CustomerBuilder */
    private $_customerBuilder;

    /** @var  \Magento\ObjectManager */
    private $_objectManager;

    /** @var  array */
    private $_customerData;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerService = $this->_objectManager->get(
            'Magento\Customer\Service\V1\CustomerServiceInterface'
        );
        $this->_addressService = $this->_objectManager->get(
            'Magento\Customer\Service\V1\CustomerAddressServiceInterface'
        );
        $this->_coreRegistry = $this->_objectManager->get('Magento\Core\Model\Registry');
        $this->_context = $this->_objectManager->get('Magento\Backend\Block\Template\Context');
        $this->_backendSession = $this->_context->getBackendSession();
        $this->_customerBuilder = $this->_objectManager->get(
            'Magento\Customer\Service\V1\Dto\CustomerBuilder'
        );
        $this->_addressBuilder = $this->_objectManager->get(
            'Magento\Customer\Service\V1\Dto\AddressBuilder'
        );

        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        /** @var \Magento\Customer\Service\V1\Dto\Customer $customer */
        $customer = $this->_customerService->getCustomer(1);
        $this->_customerData = [
            'customer_id' => $customer->getCustomerId(),
            'account' => $customer->getAttributes(),
        ];
        $this->_customerData['account']['id'] = $customer->getCustomerId();
        $addresses = $this->_addressService->getAddresses(1);
        foreach ($addresses as $addressDto) {
            $this->_customerData['address'][$addressDto->getId()] = $addressDto->getAttributes();
            $this->_customerData['address'][$addressDto->getId()]['id'] = $addressDto->getId();
        }
        $this->_backendSession->setCustomerData($this->_customerData);
    }

    public function tearDown()
    {
        $this->_backendSession->unsCustomerData();
        $this->_coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function testInitForm()
    {
        $block = $this->getMockBuilder('Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses')
            ->setConstructorArgs(
                [
                    'context' => $this->_context,
                    'registry' => $this->_coreRegistry,
                    'formFactory' => $this->_objectManager->get('Magento\Data\FormFactory'),
                    'systemStore' => $this->_objectManager->get('Magento\Core\Model\System\Store'),
                    'coreData' => $this->_objectManager->get('Magento\Core\Helper\Data'),
                    'jsonEncoder' => $this->_objectManager->get('Magento\Json\EncoderInterface'),
                    'regionFactory' => $this->_objectManager->get(
                            'Magento\Customer\Model\Renderer\RegionFactory'
                        ),
                    'metadataFormFactory' =>
                        $this->_objectManager->get('Magento\Customer\Model\Metadata\FormFactory'),
                    'customerHelper' => $this->_objectManager->get('Magento\Customer\Helper\Data'),
                    'addressHelper' => $this->_objectManager->get('Magento\Customer\Helper\Address'),
                    'customerService' =>
                        $this->_objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface'),
                    'metadataService' =>$this->_objectManager->get(
                            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface'
                        ),
                    'addressBuilder' => $this->_addressBuilder,
                    'customerBuilder' => $this->_customerBuilder,
                    'attributeMetadataBuilder' =>$this->_objectManager->get(
                            'Magento\Customer\Service\V1\Dto\Eav\AttributeMetadataBuilder'
                        )
                ]
            )
            ->setMethods(['assign'])
            ->getMock();

        $customerDto = $this->_customerBuilder->populateWithArray($this->_customerData['account'])->create();
        $block->expects($this->at(0))
            ->method('assign')
            ->with('customer', $customerDto);

        $addressCollection = [];
        foreach ($this->_customerData['address'] as $key => $addressData) {
            $addressCollection[$key] = $this->_addressBuilder->populateWithArray($addressData)->create();
        }
        $block->expects($this->at(1))
            ->method('assign')
            ->with('addressCollection', $addressCollection);

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
        /** @var \Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses $block */
        $block = $this->_objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\Addresses',
                '',
                [
                    'registry' => $this->_coreRegistry,
                    'context' => $this->_context,
                ]
            );

        $html = $block->initForm()->toHtml();

        $this->assertContains('<li class="address-list-item" id="address_item_1" data-item="1">', $html);
        $this->assertContains('<a href="#form_address_item_3"', $html);
        $this->assertContains('<div class="address-item-edit-content"', $html);
        $this->assertContains('id="form_address_item_1" data-item="1"', $html);
        $this->assertContains('{"name": "address_item_1"}}', $html);
        $this->assertContains('<input id="_item1prefix" name="address[1][prefix]"', $html);
    }
}
