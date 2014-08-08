<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Helper;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\Data\Customer as CustomerService;
use Magento\Customer\Service\V1\Data\CustomerBuilder;

class Customer extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/customerAccounts';
    const SERVICE_NAME = 'customerCustomerAccountServiceV1';
    const SERVICE_VERSION = 'V1';

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
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const PASSWORD = 'test@123';

    const ADDRESS_CITY1 = 'CityM';
    const ADDRESS_CITY2 = 'CityX';
    const ADDRESS_REGION_CODE1 = 'AL';
    const ADDRESS_REGION_CODE2 = 'AL';

    /** @var AddressBuilder */
    private $addressBuilder;

    /** @var CustomerDetailsBuilder */
    private $customerDetailsBuilder;

    /** @var CustomerBuilder */
    private $customerBuilder;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->addressBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\AddressBuilder'
        );

        $this->customerDetailsBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerDetailsBuilder'
        );

        $this->customerBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\Data\CustomerBuilder'
        );
    }

    public function createSampleCustomer()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateCustomer'
            ]
        ];
        $customerDetailsAsArray = $this->createSampleCustomerDetailsData()->__toArray();
        $requestData = ['customerDetails' => $customerDetailsAsArray, 'password' => self::PASSWORD];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        return $customerData;
    }

    /**
     * @return CustomerDetails
     */
    public function createSampleCustomerDetailsData()
    {
        $this->addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(
                Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\RegionBuilder')
                    ->setRegionCode(self::ADDRESS_REGION_CODE1)
                    ->setRegion('Alabama')
                    ->setRegionId(1)
                    ->create()
            )
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity(self::ADDRESS_CITY1)
            ->setFirstname('John')
            ->setLastname('Smith');
        $address1 = $this->addressBuilder->create();

        $this->addressBuilder
            ->setCountryId('US')
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(
                Bootstrap::getObjectManager()->create('\Magento\Customer\Service\V1\Data\RegionBuilder')
                    ->setRegionCode(self::ADDRESS_REGION_CODE2)
                    ->setRegion('Alabama')
                    ->setRegionId(1)
                    ->create()
            )
            ->setStreet(['Black str, 48', 'Building D'])
            ->setCity(self::ADDRESS_CITY2)
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');

        $address2 = $this->addressBuilder->create();

        $customerData = $this->createSampleCustomerDataObject();
        $customerDetails = $this->customerDetailsBuilder->setAddresses([$address1, $address2])
            ->setCustomer($customerData)
            ->create();
        return $customerDetails;
    }

    /**
     * Create customer using setters.
     *
     * @return Customer
     */
    public function createSampleCustomerDataObject()
    {
        $customerData = [
            CustomerService::FIRSTNAME => self::FIRSTNAME,
            CustomerService::LASTNAME => self::LASTNAME,
            CustomerService::EMAIL => 'janedoe' . uniqid() . '@example.com',
            CustomerService::CONFIRMATION => self::CONFIRMATION,
            CustomerService::CREATED_AT => self::CREATED_AT,
            CustomerService::CREATED_IN => self::STORE_NAME,
            CustomerService::DOB => self::DOB,
            CustomerService::GENDER => self::GENDER,
            CustomerService::GROUP_ID => self::GROUP_ID,
            CustomerService::MIDDLENAME => self::MIDDLENAME,
            CustomerService::PREFIX => self::PREFIX,
            CustomerService::STORE_ID => self::STORE_ID,
            CustomerService::SUFFIX => self::SUFFIX,
            CustomerService::TAXVAT => self::TAXVAT,
            CustomerService::WEBSITE_ID => self::WEBSITE_ID,
            CustomerService::CUSTOM_ATTRIBUTES_KEY => [
                [
                    'attribute_code' => 'disable_auto_group_change',
                    'value' => '0'
                ]
            ]
        ];
        return $this->customerBuilder->populateWithArray($customerData)->create();
    }
}
