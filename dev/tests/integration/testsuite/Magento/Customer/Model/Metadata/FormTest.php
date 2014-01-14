<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /**
     * @var array
     */
    protected $_attributes = [];

    /**
     * @var Magento\App\RequestInterface
     */
    protected $_request;

    /** @var array */
    protected $_expected = [];

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_formFactory = $objectManager
            ->create('Magento\Customer\Model\Metadata\FormFactory');

        $this->_requestData = [
            'id' => 14,
            'default_shipping' => true,
            'default_billing' => false,
            'company' => 'Company Name',
            'fax' => '(555) 555-5555',
            'middlename' => 'Mid',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => 'S45',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'street' => ['7700 W Parmer Ln'],
            'city' => 'Austin',
            'country_id' => 'US',
            'postcode' => '78620',
            'telephone' => '5125125125',
            'region_id' => 0,
            'region' => 'Texas',
        ];

        $this->_expected = $this->_requestData;
        /** Unset data which is not part of the form */
        unset($this->_expected['id']);
        unset($this->_expected['default_shipping']);
        unset($this->_expected['default_billing']);
        unset($this->_expected['middlename']);
        unset($this->_expected['prefix']);
        unset($this->_expected['suffix']);

        $this->_request = $objectManager->get('Magento\App\RequestInterface');
        $this->_request->setParams($this->_requestData);
    }

    public function testCompactData()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            []
        );
        $addressData = $addressForm->extractData($this->_request);
        $attributeValues = $addressForm->compactData($addressData);
        $this->assertEquals($this->_expected, $attributeValues);
    }

    public function testGetAttributes()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            []
        );
        $attributes = $addressForm->getAttributes();
    }

    public function testGetUserAttributes()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            []
        );
        $attributes = $addressForm->getUserAttributes();
        $this->assertEmpty($attributes);
    }

    public function testGetSystemAttributes()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            []
        );
        $attributes = $addressForm->getSystemAttributes();
        $this->assertCount(15, $attributes);
    }
}