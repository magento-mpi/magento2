<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata;

use Magento\TestFramework\Helper\Bootstrap;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $_form;

    /** @var array */
    protected $_attributes;

    /** @var \Magento\Framework\App\RequestInterface */
    protected $_request;

    /** @var array */
    protected $_expected;

    /** @var array */
    protected $_requestData = array();

    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var FormFactory $formFactory */
        $formFactory = $objectManager->create('Magento\Customer\Model\Metadata\FormFactory');
        $this->_form = $formFactory->create('customer_address', 'customer_address_edit');

        $this->_attributes = array(
            'id' => 14,
            'default_shipping' => 1,
            'default_billing' => 0,
            'company' => 'Company Name',
            'fax' => '(555) 555-5555',
            'middlename' => 'Mid',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => '',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'street' => array('2211 North First Street'),
            'city' => 'San Jose',
            'country_id' => 'US',
            'postcode' => '95131',
            'telephone' => '5125125125',
            'region_id' => 12,
            'region' => 'California'
        );

        $requestData = array(
            'company' => 'Company Name',
            'fax' => '(555) 555-5555',
            'middlename' => 'Mid',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => '',
            'firstname' => 'New Name',
            'lastname' => 'Doe',
            'street' => array('2211 New Street'),
            'city' => 'San Jose',
            'country_id' => 'US',
            'postcode' => '95131',
            'telephone' => '5125125125',
            'region_id' => 12,
            'region' => 'California'
        );
        $this->_request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $this->_request->setParams($requestData);

        $this->_expected = array_merge($this->_attributes, $requestData);

        unset($this->_expected['id']);
        unset($this->_expected['default_shipping']);
        unset($this->_expected['default_billing']);
        unset($this->_expected['middlename']);
        unset($this->_expected['prefix']);
        unset($this->_expected['suffix']);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCompactData()
    {
        $attributeValues = $this->_form->compactData($this->_form->extractData($this->_request));
        $this->assertEquals($this->_expected, $attributeValues);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetAttributes()
    {
        $expectedAttributes = array(
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
        $this->assertEquals($expectedAttributes, array_keys($this->_form->getAttributes()));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSystemAttributes()
    {
        $this->assertCount(15, $this->_form->getSystemAttributes());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/attribute_user_defined_address.php
     */
    public function testGetUserAttributes()
    {
        $expectedAttributes = array('address_user_attribute');
        $this->assertEquals($expectedAttributes, array_keys($this->_form->getUserAttributes()));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testRestoreData()
    {
        $attributeValues = $this->_form->restoreData($this->_form->extractData($this->_request));
        $this->assertEquals($this->_expected, $attributeValues);
    }
}
