<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\RegionBuilder;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class CustomerAccountServiceTest
 */
class CustomerAccountServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = "customerCustomerAccountServiceV1";
    const SERVICE_VERSION = "V1";
    const RESOURCE_PATH = "/V1/customerAccounts";

    const CONFIRMATION = 'a4fg7h893e39d';
    const CREATED_AT = '2013-11-05';
    const CREATED_IN = 'default';
    const STORE_NAME = 'Store Name';
    const DOB = '1970-01-01';
    const GENDER = 'Male';
    const GROUP_ID = 1;
    const MIDDLENAME = 'A';
    const PREFIX = 'Mr.';
    const STORE_ID = 1;
    const SUFFIX = 'Esq.';
    const TAXVAT = '12';
    const WEBSITE_ID = 1;

    /** Sample values for testing */
    const ID = 1;
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const NAME = 'J';
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_VALUE = 'attribute_value';

    /** @var CustomerAccountServiceInterface */
    private $customerAccountService;

    /** @var AddressBuilder */
    private $_addressBuilder;

    /** @var CustomerBuilder */
    private $_customerBuilder;

    /** @var CustomerDetailsBuilder */
    private $_customerDetailsBuilder;


    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->customerAccountService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        unset($this->customerAccountService);
    }

    public function testCreateCustomer()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerAccountServiceV1CreateAccount'
            ]
        ];
        $customerDetailsAsArray = $this->_createSampleCustomerDetailsData()->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray];
        /** @var $customerData Customer */
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotNull($customerData['id']);;
    }

    /**
     * @return CustomerDetails
     */
    private function _createSampleCustomerDetailsData()
    {
        $this->_addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\AddressBuilder'
        );
        $this->_customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerBuilder'
        );
        $this->_customerDetailsBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerDetailsBuilder'
        );

        $this->_addressBuilder
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion((new RegionBuilder())->populateWithArray([
                        'region_code' => 'AL',
                        'region' => 'Alabama',
                        'region_id' => 1
                    ])->create() )
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address1 = $this->_addressBuilder->create();

        $this->_addressBuilder
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion((new RegionBuilder())->populateWithArray([
                        'region_code' => 'AL',
                        'region' => 'Alabama',
                        'region_id' => 1
                    ])->create())
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');

        $address2 = $this->_addressBuilder->create();

        $customerData = $this->_createCustomerData();
        $customer = $this->_customerBuilder->populateWithArray($customerData)->create();
        $customerDetails = $this->_customerDetailsBuilder->setAddresses([$address1, $address2])->setCustomer($customer)
            ->create();
        return $customerDetails;
    }

    /**
     * Create customer using setters.
     *
     * @return array
     */
    private function _createCustomerData()
    {
        return [
            'firstname' => self::FIRSTNAME,
            'lastname' => self::LASTNAME,
            'email' => 'janedoe'. rand() .'@example.com',
            'confirmation' => self::CONFIRMATION,
            'created_at' => self::CREATED_AT,
            'created_in' => self::STORE_NAME,
            'dob' => self::DOB,
            'gender' => self::GENDER,
            'group_id' => self::GROUP_ID,
            'middlename' => self::MIDDLENAME,
            'prefix' => self::PREFIX,
            'store_id' => self::STORE_ID,
            'suffix' => self::SUFFIX,
            'taxvat' => self::TAXVAT,
            'website_id' => self::WEBSITE_ID
        ];
    }


}
