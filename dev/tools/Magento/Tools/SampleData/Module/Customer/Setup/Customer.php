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


/**
 * Class Customer
 */
class Customer implements SetupInterface
{
    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    protected $addressBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
     */
    protected $customerDetailsBuilder;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccountServiceInterface;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var array $customerDataProfile
     */
    protected $customerDataProfile =
        [
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
    protected $customerDataAddress =
        [
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
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * \Magento\Directory\Model\CountryFactory $countryFactory
     * @param array $fixtures
     */
    function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        $fixtures = [
            'Customer/customer_profile.csv'
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->customerBuilder = $customerBuilder;
        $this->addressBuilder = $addressBuilder;
        $this->customerDetailsBuilder = $customerDetailsBuilder;
        $this->customerAccountService = $customerAccountService;
        $this->customerFactory = $customerFactory;
        $this->countryFactory = $countryFactory;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach ($this->fixtures as $file) {

            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                // Collect customer profile and addresses data
                $customerData['profile'] = $this->convertRowData($row, $this->customerDataProfile);
                $customerData['address'] = $this->convertRowData($row, $this->customerDataAddress);
                $customerData['address']['region_id'] = $this->getRegionId($customerData['address']);
                $customerProfile = $this->customerBuilder->populateWithArray($customerData['profile'])->create();
                $addresses[] = $this->addressBuilder->populateWithArray($customerData['address'])->create();
                // Build customer details object
                $customerDetails = $this->customerDetailsBuilder
                    ->setCustomer($customerProfile)
                    ->setAddresses($addresses)
                    ->create();
                // Save customer
                if ($this->customerAccountService->isEmailAvailable($customerProfile->getEmail(), $customerProfile->getWebsiteId())) {
                    $customer = $this->customerAccountService->createCustomer($customerDetails);
                    $customerId = $customer->getId();
                } else {
                    $customerId = $this->customerAccountService->getCustomerByEmail($customerProfile->getEmail())->getId();
                }
                $this->updateCustomerPassword($customerId, $row['password']);
                echo '.';
            }
            echo "\n";
        }
    }

    /**
     * @param $row
     * @param $data
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
                if ($field == 'password')
                {
                    continue;
                }
                $data[$field] = $value;
            }
        }
        return $data;
    }

    /**
     * @param $address
     * @return mixed
     */
    protected function getRegionId($address)
    {
        $country = $this->countryFactory->create()->loadByCode($address['country_id']);
        return $country->getRegionCollection()->addFieldToFilter('name', $address['region'])->getFirstItem()->getId();
    }

    /**
     * @param $customerId
     * @param $password
     */
    protected function updateCustomerPassword($customerId, $password)
    {
        $customerAccountData = $this->customerAccountService->getCustomer($customerId);
        $customerModel = $this->customerFactory->create()->setWebsiteId($customerAccountData->getWebsiteId());
        $customerModel->loadByEmail($customerAccountData->getEmail())->setPassword($password)->save();
    }
}
