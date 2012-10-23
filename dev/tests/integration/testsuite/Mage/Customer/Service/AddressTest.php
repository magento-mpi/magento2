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

/**
 * Test class for Mage_Customer_Service_Address
 */
class Mage_Customer_Service_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Service_Address
     */
    protected $_service;

    protected function setUp()
    {
        $this->_service = new Mage_Customer_Service_Address();
    }

    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with no addresses.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testGetByCustomerIdNoAddresses()
    {
        $addresses = $this->_service->getByCustomerId(1);
        $this->assertInternalType('array', $addresses);
        $this->assertEmpty($addresses);
    }

    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with existing multiple addresses.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Mage/Customer/_files/customer_two_addresses.php
     */
    public function testGetByCustomerId()
    {
        $addresses = $this->_service->getByCustomerId(1);
        $this->assertInternalType('array', $addresses);
        $this->assertCount(2, $addresses);
        foreach ($addresses as $addressId => $addressModel) {
            $this->assertInstanceOf('Mage_Customer_Model_Address', $addressModel);
            $this->assertEquals($addressId, $addressModel->getId());
        }
    }

    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with invalid customer id.
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage The customer with the specified ID not found.
     */
    public function testGetByCustomerIdWithInvalidCustomerId()
    {
        $this->_service->getByCustomerId(1);
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @dataProvider createDataProvider
     *
     * @param array $addressData
     */
    public function testCreate(array $addressData)
    {
        $customerId = 1;
        $address = $this->_service->create($addressData, 1);
        $this->assertInstanceOf('Mage_Customer_Model_Address', $address);
        $this->assertGreaterThan(0, $address->getId());
        $this->assertEquals($customerId, $address->getCustomerId());

        $expectedData = $addressData;
        ksort($expectedData);

        $actualData = Mage::getModel('Mage_Customer_Model_Address')->load($address->getId())
            ->toArray(array_keys($expectedData));
        ksort($actualData);

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * Data provider for testCreate method
     *
     * @return array
     */
    public function createDataProvider()
    {
        return array(
            'All data' => array(
                array(
                    'prefix' => 'Mrs.',
                    'firstname' => 'Linda',
                    'middlename' => 'G.',
                    'lastname' => 'Jones',
                    'suffix' => 'Suffix',
                    'company' => 'Vitagee',
                    'street' => "3083 Eagles Nest Drive\nHoopa",
                    'country_id' => 'US',
                    'region_id' => '12', // California
                    'city' => 'Los Angeles',
                    'postcode' => '95546',
                    'fax' => '55512450056',
                    'telephone' => '55512450000',
                    'vat_id' => '556-70-1739',
                )
            ),
            'Mandatory data' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                )
            ),
        );
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Mage/Customer/_files/customer_address.php
     * @dataProvider updateDataProvider
     *
     * @param array $addressData
     */
    public function testUpdate(array $addressData)
    {
        $addressId = 1;
        $address = $this->_service->update($addressId, $addressData);
        $this->assertInstanceOf('Mage_Customer_Model_Address', $address);
        $this->assertEquals($addressId, $address->getId());

        $expectedData = array_merge($address->getData(), $addressData);
        ksort($expectedData);

        $actualData = Mage::getModel('Mage_Customer_Model_Address')->load($addressId)->getData();
        ksort($actualData);

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * Data provider for testUpdate method
     *
     * @return array
     */
    public function updateDataProvider()
    {
        return array(
            'All data' => array(
                array(
                    'prefix' => 'Mrs.',
                    'firstname' => 'Glenn',
                    'middlename' => 'G.',
                    'lastname' => 'James',
                    'suffix' => 'S.',
                    'company' => 'Olympic Sports',
                    'street' => "3061 Bell Street",
                    'country_id' => 'US',
                    'region_id' => '43', // New York
                    'city' => 'New York',
                    'postcode' => '10036',
                    'fax' => '12355447766',
                    'telephone' => '12355447700',
                    'vat_id' => '777-70-1500',
                )
            ),
            'Only firstname' => array(
                array(
                    'firstname' => 'Jill',
                )
            ),
            'Only lastname' => array(
                array(
                    'lastname' => 'Jones',
                )
            ),
            'Empty data' => array(
                array()
            ),
        );
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     *
     * @expectedException Magento_Validator_Exception
     * @dataProvider createErrorsDataProvider
     *
     * @param array $addressData
     * @param array|null $expectedMessagesKeys
     * @param string|null $expectedMessage
     * @throws Magento_Validator_Exception
     */
    public function testCreateErrors(array $addressData, array $expectedMessagesKeys = array(), $expectedMessage = '')
    {
        $customerId = 1;
        $this->setExpectedException('Magento_Validator_Exception', $expectedMessage);

        try {
            $this->_service->create($addressData, $customerId);
        } catch (Magento_Validator_Exception $exception) {
            if ($expectedMessagesKeys) {
                $this->assertEquals($expectedMessagesKeys, array_keys($exception->getMessages()));
            }
            throw $exception;
        }
    }

    /**
     * Data provider for testCreateErrors method
     *
     * @return array
     */
    public function createErrorsDataProvider()
    {
        return array(
            'Empty mandatory data' => array(
                array(
                    'firstname' => '',
                    'lastname' => '',
                    'street' => '',
                    'country_id' => '',
                    'city' => '',
                    'postcode' => '',
                    'telephone' => '',
                ), array('city', 'country_id', 'firstname', 'lastname', 'postcode', 'street', 'telephone')
            ),
            'Empty data' => array(
                array(), array('city', 'country_id', 'firstname', 'lastname', 'postcode', 'street', 'telephone')
            ),
            'Empty firstname' => array(
                array(
                    'firstname' => '',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                ), array('firstname')
            ),
            'Read-only entity_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'entity_id' => 100
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only increment_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'increment_id' => 100
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only entity_type_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'entity_type_id' => 1
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only attribute_set_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Green str, 67',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'attribute_set_id' => 0
                ), null, 'Read-only property cannot be changed.'
            ),
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage The customer with the specified ID not found.
     */
    public function testCreateWithInvalidCustomer()
    {
        $this->_service->create(array(
            'firstname' => 'John',
            'lastname' => 'Smith',
            'street' => 'Green str, 67',
            'country_id' => 'AL',
            'city' => 'CityM',
            'postcode' => '75477',
            'telephone' => '3468676',
        ), 1);
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Mage/Customer/_files/customer_address.php
     *
     * @expectedException Magento_Validator_Exception
     * @dataProvider updateErrorsDataProvider
     *
     * @param array $addressData
     * @param array|null $expectedMessagesKeys
     * @param string|null $expectedMessage
     * @throws Magento_Validator_Exception
     */
    public function testUpdateErrors(array $addressData, array $expectedMessagesKeys = array(), $expectedMessage = '')
    {
        $addressId = 1;
        $this->setExpectedException('Magento_Validator_Exception', $expectedMessage);

        try {
            $this->_service->update($addressId, $addressData);
        } catch (Magento_Validator_Exception $exception) {
            if ($expectedMessagesKeys) {
                $this->assertEquals($expectedMessagesKeys, array_keys($exception->getMessages()));
            }
            throw $exception;
        }
    }

    /**
     * Data provider for testUpdateErrors method
     *
     * @return array
     */
    public function updateErrorsDataProvider()
    {
        return array(
            'Empty mandatory data' => array(
                array(
                    'firstname' => '',
                    'lastname' => '',
                    'street' => '',
                    'country_id' => '',
                    'city' => '',
                    'postcode' => '',
                    'telephone' => '',
                ), array('city', 'country_id', 'firstname', 'lastname', 'postcode', 'street', 'telephone')
            ),
            'Empty street' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => '',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                ), array('street')
            ),
            'Read-only attribute_set_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'attribute_set_id' => 0,
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only increment_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'increment_id' => 1,
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only entity_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'entity_id' => 1,
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only entity_type_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'entity_type_id' => 1,
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only entity_parent_id' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'parent_id' => 1,
                ), null, 'Read-only property cannot be changed.'
            ),
            'Read-only created_at' => array(
                array(
                    'firstname' => 'John',
                    'lastname' => 'Smith',
                    'street' => 'Foo str.',
                    'country_id' => 'AL',
                    'city' => 'CityM',
                    'postcode' => '75477',
                    'telephone' => '3468676',
                    'created_at' => date('Y-m-d h:i:s'),
                ), null, 'Read-only property cannot be changed.'
            )
        );
    }
}
