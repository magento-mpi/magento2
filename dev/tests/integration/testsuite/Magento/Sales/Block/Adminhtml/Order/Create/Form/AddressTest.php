<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Form;

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Create\Form\Address
 *
 * @magentoAppArea adminhtml
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Sales\Block\Adminhtml\Order\Create\Form\Address */
    protected $_addressBlock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\CustomerAddressServiceInterface */
    protected $_addressService;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_addressService = $this->getMock('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $sessionQuoteMock = $this->getMockBuilder(
            'Magento\Backend\Model\Session\Quote'
        )->disableOriginalConstructor()->setMethods(
            array('getCustomerId', 'getStore', 'getStoreId', 'getQuote')
        )->getMock();
        $sessionQuoteMock->expects($this->any())->method('getCustomerId')->will($this->returnValue(1));

        $this->_addressBlock = $layout->createBlock(
            'Magento\Sales\Block\Adminhtml\Order\Create\Form\Address',
            'address_block' . rand(),
            array('addressService' => $this->_addressService, 'sessionQuote' => $sessionQuoteMock)
        );
        parent::setUp();
    }

    public function testGetAddressCollection()
    {
        $addressData = $this->_getAddresses();
        $this->_addressService->expects($this->any())->method('getAddresses')->will($this->returnValue($addressData));
        $this->assertEquals($addressData, $this->_addressBlock->getAddressCollection());
    }

    public function testGetAddressCollectionJson()
    {
        $addressData = $this->_getAddresses();
        $this->_addressService->expects($this->any())->method('getAddresses')->will($this->returnValue($addressData));
        $expectedOutput = '[
            {
                "firstname": false,
                "lastname": false,
                "company": false,
                "street": "",
                "city": false,
                "country_id": "US",
                "region": false,
                "region_id": false,
                "postcode": false,
                "telephone": false,
                "fax": false,
                "vat_id": false
            },
            {
                "firstname": "FirstName1",
                "lastname": "LastName1",
                "company": false,
                "street": "Street1",
                "city": false,
                "country_id": false,
                "region": false,
                "region_id": false,
                "postcode": false,
                "telephone": false,
                "fax": false,
                "vat_id": false
            },
            {
                "firstname": "FirstName2",
                "lastname": "LastName2",
                "company": false,
                "street": "Street2",
                "city": false,
                "country_id": false,
                "region": false,
                "region_id": false,
                "postcode": false,
                "telephone": false,
                "fax": false,
                "vat_id": false
            }
        ]';
        $expectedOutput = str_replace(array('    ', "\n", "\r"), '', $expectedOutput);
        $expectedOutput = str_replace(': ', ':', $expectedOutput);

        $this->assertEquals($expectedOutput, $this->_addressBlock->getAddressCollectionJson());
    }

    public function testGetAddressAsString()
    {
        $address = $this->_getAddresses()[0];
        $expectedResult = "FirstName1 LastName1, Street1, ,  , ";
        $this->assertEquals($expectedResult, $this->_addressBlock->getAddressAsString($address));
    }

    /**
     * Test \Magento\Sales\Block\Adminhtml\Order\Create\Form\Address::_prepareForm() indirectly.
     */
    public function testGetForm()
    {
        $expectedFields = array(
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
            'company',
            'street',
            'city',
            'country_id',
            'region',
            'region_id',
            'postcode',
            'telephone',
            'fax',
            'vat_id'
        );
        $form = $this->_addressBlock->getForm();
        $this->assertEquals(1, $form->getElements()->count(), "Form has invalid number of fieldsets");
        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->getElements()[0];
        $this->assertEquals(
            count($expectedFields),
            $fieldset->getElements()->count(),
            "Form has invalid number of fields"
        );
        /** @var \Magento\Data\Form\Element\AbstractElement $element */
        foreach ($fieldset->getElements() as $element) {
            $this->assertTrue(
                in_array($element->getId(), $expectedFields),
                sprintf('Unexpected field "%s" in form.', $element->getId())
            );
        }

        /** @var \Magento\Data\Form\Element\Select $countryIdField */
        $countryIdField = $fieldset->getElements()->searchById('country_id');
        $this->assertSelectCount('option', 247, $countryIdField->getElementHtml());
    }

    /**
     * @return \Magento\Customer\Service\V1\Data\Address[]
     */
    protected function _getAddresses()
    {
        /** @var \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder */
        $addressBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Data\AddressBuilder');
        $addressBuilder->populateWithArray(
            array('id' => 1, 'street' => 'Street1', 'firstname' => 'FirstName1', 'lastname' => 'LastName1')
        );
        $addressData[] = $addressBuilder->create();
        $addressBuilder->populateWithArray(
            array('id' => 2, 'street' => 'Street2', 'firstname' => 'FirstName2', 'lastname' => 'LastName2')
        );
        $addressData[] = $addressBuilder->create();
        return $addressData;
    }
}
