<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

/**
 * Integration test for \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
 */
class CustomerDetailsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * CustomerDetails builder
     *
     * @var CustomerDetailsBuilder
     */
    private $_builder;

    /**
     * Customer builder
     *
     * @var CustomerBuilder
     */
    private $_customerBuilder;

    /**
     * Address builder
     *
     * @var AddressBuilder
     */
    private $_addressBuilder;

    protected function setUp()
    {
        $this->markTestSkipped('Will be removed as part of MAGETWO-30671');
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_builder = $this->_objectManager->create('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $this->_customerBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Data\CustomerBuilder');
        $this->_addressBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Data\AddressBuilder');
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
        if (!is_null($expectedCustomer)) {
            $expectedCustomer = $this->_customerBuilder->populateWithArray($expectedCustomer)->create();
        }
        if (!is_null($customer)) {
            $customer = $this->_customerBuilder->populateWithArray($customer)->create();
        }
        if (!is_null($expectedAddresses)) {
            $addressArray = array();
            foreach ($expectedAddresses as $expectedAddress) {
                $addressArray[] = $this->_addressBuilder->populateWithArray($expectedAddress)->create();
            }
            $expectedAddresses = $addressArray;
        }
        if (!is_null($addresses)) {
            $addressArray = array();
            foreach ($addresses as $address) {
                $addressArray[] = $this->_addressBuilder->populateWithArray($address)->create();
            }
            $addresses = $addressArray;
        }
        if (!empty($customer)) {
            $this->_builder->setCustomer($customer);
        }
        $customerDetails = $this->_builder->setAddresses($addresses)->create();
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Data\CustomerDetails', $customerDetails);
        $this->assertEquals($expectedCustomer, $customerDetails->getCustomer());
        $this->assertEquals($expectedAddresses, $customerDetails->getAddresses());
    }

    public function createDataProvider()
    {

        $customerData = array(
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'test firstname',
            'lastname' => 'test lastname',
            'email' => 'example@domain.com',
            'default_billing' => '_item1',
            'password' => '123123q'
        );

        $addressData = array(
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
            'street' => array('7700 W Parmer Ln'),
            'city' => 'Austin',
            'country_id' => 'US',
            'postcode' => '78620',
            'telephone' => '5125125125',
            'region' => array('region_id' => 0, 'region' => 'Texas')
        );

        return array(
            array($customerData, array($addressData, $addressData), $customerData, array($addressData, $addressData)),
            array(null, array($addressData, $addressData), array(), array($addressData, $addressData))
        );
    }
}
