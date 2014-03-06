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


    /**
     * @param $customer
     * @param $addresses
     * @param $expectedCustomer
     * @param $expectedAddresses
     * @dataProvider createDataProvider
     */
    public function testCreate($customer, $addresses, $expectedCustomer, $expectedAddresses)
    {
        if (!empty($customer)) {
            $this->_builder->setCustomer($customer);
        }
        $customerDetails = $this->_builder->setAddresses($addresses)->create();
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Dto\CustomerDetails', $customerDetails);
        $this->assertEquals($expectedCustomer, $customerDetails->getCustomer());
        $this->assertEquals($expectedAddresses, $customerDetails->getAddresses());
    }

    public function createDataProvider()
    {
        $customerData = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'example@domain.com',
            'default_billing' => '_item1',
            'password' => '123123q'
        ];

        $addressData = [
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
        $customerBuilder = new CustomerBuilder();
        $emptyCustomer = $customerBuilder->populateWithArray([])->create();
        $customer = $customerBuilder->populateWithArray($customerData)->create();
        $addressBuilder = new AddressBuilder(new RegionBuilder());
        $address = $addressBuilder->populateWithArray($addressData)->create();
        return [
            [null, null, $emptyCustomer, null],
            [$customer, null, $customer, null],
            [null, [], $emptyCustomer, []],
            [$customer, [$address], $customer, [$address]],
            [$customer, [$address, $address], $customer, [$address, $address]],
            [null, [$address, $address], $emptyCustomer, [$address, $address]],
        ];
    }

    /**
     * @param $data
     * @param $expectedCustomer
     * @param $expectedAddresses
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulateWithArray($data, $expectedCustomer, $expectedAddresses)
    {
        $customerDetails = $this->_builder->populateWithArray($data)->create();
        $customerDetailsA = $this->_builder->populateWithArray($customerDetails->__toArray())->create();
        $this->assertEquals($customerDetailsA, $customerDetails);
        $this->assertEquals($expectedCustomer, $customerDetails->getCustomer());
        $this->assertEquals($expectedAddresses, $customerDetails->getAddresses());
    }

    /**
     * @param $data
     * @param $expectedCustomer
     * @param $expectedAddresses
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulate($data, $expectedCustomer, $expectedAddresses)
    {
        $customerDetails = $this->_builder->populateWithArray($data)->create();
        $customerDetailsA = $this->_builder->populate($customerDetails)->create();
        $this->assertEquals($customerDetailsA, $customerDetails);
        $this->assertEquals($expectedCustomer, $customerDetails->getCustomer());
        $this->assertEquals($expectedAddresses, $customerDetails->getAddresses());
    }

    public function populateWithArrayDataProvider()
    {
        $customer = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'example@domain.com',
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

        $customerBuilder = new CustomerBuilder();
        $emptyCustomer = $customerBuilder->populateWithArray([])->create();
        $customerSdo = $customerBuilder->populateWithArray($customer)->create();
        $addressBuilder = new AddressBuilder(new RegionBuilder());
        $addressSdoA = $addressBuilder->populateWithArray($address1)->create();
        $addressSdoB = $addressBuilder->populateWithArray($address2)->create();
        return [
            [[], $emptyCustomer, null],
            [['customer' => $customer], $customerSdo, null],
            [['customer' => $customer, 'addresses' => null], $customerSdo, null],
            [
                ['customer' => $customer, 'addresses' => [$address1, $address2]],
                $customerSdo,
                [$addressSdoA, $addressSdoB]
            ],
            [
                ['addresses' => [$address1, $address2]],
                $emptyCustomer,
                [$addressSdoA, $addressSdoB]
            ],
            [
                ['customer' => null, 'addresses' => [$address1, $address2]],
                $emptyCustomer,
                [$addressSdoA, $addressSdoB]
            ],
        ];
    }

    public function testMergeDtos()
    {
        $customer = [
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'example@domain.com',
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
            'email' => 'example@domain.com',
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

