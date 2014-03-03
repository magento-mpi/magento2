<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Integration test for \Magento\Customer\Service\V1\Dto\CustomerDetailsBuilder
 */
class CustomerDetailsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * CustomerDetails builder
     *
     * @var CustomerDetailsBuilder
     */
    private $_builder;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_builder =
            $this->_objectManager->create('Magento\Customer\Service\V1\Dto\CustomerDetailsBuilder');
    }

    public function testCreate()
    {
        $customerDetails = $this->_builder->create();
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Dto\CustomerDetails', $customerDetails);
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Dto\Customer', $customerDetails->getCustomer());
        $this->assertEquals([], $customerDetails->getAddresses());
    }

    /**
     * @param $data
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulateWithArray($data)
    {
        $customerDetails = $this->_builder->populateWithArray($data)->create();
        $customerDetailsA = $this->_builder->populateWithArray($customerDetails->__toArray())->create();
        $this->assertEquals($customerDetailsA, $customerDetails);
    }

    /**
     * @param $data
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulate($data)
    {
        $customerDetails = $this->_builder->populateWithArray($data)->create();
        $customerDetailsA = $this->_builder->populate($customerDetails)->create();
        $this->assertEquals($customerDetailsA, $customerDetails);
    }

    public function populateWithArrayDataProvider()
    {
        $customer = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'exmaple@domain.com',
            'default_billing' => '_item1',
            'password' => '123123q'
        ];

        $address1 = [
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
            'region' => [
                'region_id' => 0,
                'region' => 'Texas',
            ],
        ];

        $address2 = [
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => array('test street'),
            'city' => 'test city',
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'id' => 2
        ];

        return [
            [[]],
            [['customer' => $customer]],
            [['customer' => $customer, 'addresses' => [$address1, $address2]]],
            [['addresses' => [$address1, $address2]]],
        ];
    }

    public function testMergeDtos()
    {
        $customer = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'exmaple@domain.com',
            'default_billing' => '_item1',
            'password' => '123123q'
        ];

        $address1 = [
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
            'region' => [
                'region_id' => 0,
                'region' => 'Texas',
            ],
        ];

        $address2 = [
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => array('test street'),
            'city' => 'test city',
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'id' => 2
        ];

        $addressMerge = [
            'id' => 2,
            'default_shipping' => true,
            'default_billing' => false,
            'company' => 'Company Name',
            'fax' => '(555) 555-5555',
            'middlename' => 'Mid',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => 'S45',
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => array('test street'),
            'city' => 'test city',
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'region' => [
                'region_id' => 0,
                'region' => 'Texas',
            ],
        ];

        $customerDetails = $this->_builder
            ->populateWithArray(['customer' => $customer, 'addresses' => [$addressMerge]])
            ->create();
        $customerDetailsC = $this->_builder
            ->populateWithArray(['customer' => $customer, 'addresses' => [$address1]])
            ->create();
        $customerDetailsA = $this->_builder
            ->populateWithArray(['customer' => $customer, 'addresses' => [$address2]])
            ->create();
        $customerDetailsB = $this->_builder->mergeDtos($customerDetailsC, $customerDetailsA);
        $this->assertEquals($customerDetails, $customerDetailsB);
    }

    public function testMergeDtoWithArray()
    {
        $customer = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'exmaple@domain.com',
            'default_billing' => '_item1',
            'password' => '123123q'
        ];

        $address1 = [
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
            'region' => [
                'region_id' => 0,
                'region' => 'Texas',
            ],
        ];

        $address2 = [
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => array('test street'),
            'city' => 'test city',
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'id' => 2
        ];

        $addressMerge = [
            'id' => 2,
            'default_shipping' => true,
            'default_billing' => false,
            'company' => 'Company Name',
            'fax' => '(555) 555-5555',
            'middlename' => 'Mid',
            'prefix' => 'Mr.',
            'suffix' => 'Esq.',
            'vat_id' => 'S45',
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'street' => array('test street'),
            'city' => 'test city',
            'country_id' => 'US',
            'postcode' => '01001',
            'telephone' => '+7000000001',
            'region' => [
                'region_id' => 0,
                'region' => 'Texas',
            ],
        ];

        $customerDetails = $this->_builder
            ->populateWithArray(['customer' => $customer, 'addresses' => [$addressMerge]])
            ->create();
        $customerDetailsC = $this->_builder
            ->populateWithArray(['customer' => $customer, 'addresses' => [$address1]])
            ->create();
        $customerDetailsB = $this->_builder->mergeDtoWithArray($customerDetailsC, ['addresses' => [$address2]]);
        $this->assertEquals($customerDetails, $customerDetailsB);
    }
}

