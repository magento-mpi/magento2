<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Customer\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Customer\Api\Data\RegionInterface;

/**
 * Class Customer
 */
class Customer implements SetupInterface
{
    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Customer\Api\Data\AddressDataBuilder
     */
    protected $addressBuilder;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressDataBuilder
     */
    protected $addressDataBuilder;

    /**
     * @var \Magento\Customer\Api\Data\RegionDataBuilder
     */
    protected $regionDataBuilder;

    /** @var \Magento\Customer\Api\AccountManagementInterface */
    protected $accountManagement;

    /**
     * @var array $customerDataProfile
     */
    protected $customerDataProfile = [
        'website_id' => '1',
        'group_id' => '1',
        'disable_auto_group_change' => '0',
        'prefix',
        'firstname' => '',
        'middlename' => '',
        'lastname' => '',
        'suffix' => '',
        'email' => '',
        'dob' => '',
        'taxvat' => '',
        'gender' => '',
        'confirmation' => false,
        'sendemail' => false
    ];

    /**
     * @var array $customerDataAddress
     */
    protected $customerDataAddress = [
        'prefix' => '',
        'firstname' => '',
        'middlename' => '',
        'lastname' => '',
        'suffix' => '',
        'company' => '',
        'street' => [
            0 => '',
            1 => ''
        ],
        'city' => '',
        'country_id' => '',
        'region' => '',
        'postcode' => '',
        'telephone' => '',
        'fax' => '',
        'vat_id' => '',
        'default_billing' => true,
        'default_shipping' => true
    ];

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Customer\Api\Data\AddressDataBuilder $addressDataBuilder
     * @param \Magento\Customer\Api\Data\RegionDataBuilder $regionDataBuilder
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Customer\Api\Data\AddressDataBuilder $addressBuilder,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Api\Data\AddressDataBuilder $addressDataBuilder,
        \Magento\Customer\Api\Data\RegionDataBuilder $regionDataBuilder,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Tools\SampleData\Logger $logger,
        $fixtures = [
            'Customer/customer_profile.csv'
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->customerBuilder = $customerBuilder;
        $this->addressBuilder = $addressBuilder;
        $this->countryFactory = $countryFactory;
        $this->addressDataBuilder = $addressDataBuilder;
        $this->regionDataBuilder = $regionDataBuilder;
        $this->accountManagement = $accountManagement;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing customers' . PHP_EOL);
        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                // Collect customer profile and addresses data
                $customerData['profile'] = $this->convertRowData($row, $this->customerDataProfile);
                $customerData['address'] = $this->convertRowData($row, $this->customerDataAddress);
                $customerData['address']['region_id'] = $this->getRegionId($customerData['address']);

                $addresses[] = $this->addressBuilder->populateWithArray($customerData['address'])->create();

                $address = $customerData['address'];
                $region = [
                    RegionInterface::REGION_ID => $address['region_id'],
                    RegionInterface::REGION => !empty($address['region']) ? $address['region'] : null,
                    RegionInterface::REGION_CODE => !empty($address['region_code']) ? $address['region_code'] : null
                ];

                $region = $this->regionDataBuilder
                    ->populateWithArray($region)
                    ->create();

                $address = $this->addressDataBuilder
                    ->populateWithArray($customerData['address'])
                    ->setRegion($region)
                    ->setDefaultBilling(true)
                    ->setDefaultShipping(true)
                    ->create();

                $customer = $this->customerBuilder->populateWithArray($customerData['profile'])
                    ->setAddresses(array($address))
                    ->create();

                $this->accountManagement->createAccount($customer, $row['password']);
                $this->logger->log('.');
            }
            $this->logger->log(PHP_EOL);
        }
    }

    /**
     * @param array $row
     * @param array $data
     * @return array $data
     */
    protected function convertRowData($row, $data)
    {
        foreach ($row as $field => $value) {
            if (isset($data[$field])) {
                if ($field == 'street') {
                    $data[$field] = unserialize($value);
                    continue;
                }
                if ($field == 'password') {
                    continue;
                }
                $data[$field] = $value;
            }
        }
        return $data;
    }

    /**
     * @param array $address
     * @return mixed
     */
    protected function getRegionId($address)
    {
        $country = $this->countryFactory->create()->loadByCode($address['country_id']);
        return $country->getRegionCollection()->addFieldToFilter('name', $address['region'])->getFirstItem()->getId();
    }
}
