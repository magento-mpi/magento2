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
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

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
    protected $customerAccount;

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
    protected $customerDataProfile;

    /**
     * @var array $customerDataAddress
     */
    protected $customerDataAddress;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Tools\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param \Magento\Tools\SampleData\Helper\StoreManager $storeManager
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccount,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Tools\SampleData\Logger $logger,
        \Magento\Tools\SampleData\Helper\StoreManager $storeManager,
        $fixtures = [
            'Customer/customer_profile.csv'
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->customerBuilder = $customerBuilder;
        $this->addressBuilder = $addressBuilder;
        $this->customerDetailsBuilder = $customerDetailsBuilder;
        $this->customerAccount = $customerAccount;
        $this->customerFactory = $customerFactory;
        $this->countryFactory = $countryFactory;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
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
                $customerData['profile'] = $this->convertRowData($row, $this->getDefaultCustomerProfile());
                $customerData['address'] = $this->convertRowData($row, $this->getDefaultCustomerAddress());
                $customerData['address']['region_id'] = $this->getRegionId($customerData['address']);
                $customerProfile = $this->customerBuilder->populateWithArray($customerData['profile'])->create();
                $addresses[] = $this->addressBuilder->populateWithArray($customerData['address'])->create();
                // Build customer details object
                $customerDetails = $this->customerDetailsBuilder
                    ->setCustomer($customerProfile)
                    ->setAddresses($addresses)
                    ->create();
                // Save customer
                $emailAvailable = $this->customerAccount
                    ->isEmailAvailable($customerProfile->getEmail(), $customerProfile->getWebsiteId());
                if ($emailAvailable) {
                    $customer = $this->customerAccount->createCustomer($customerDetails);
                    $customerId = $customer->getId();
                } else {
                    $customerId = $this->customerAccount->getCustomerByEmail($customerProfile->getEmail())->getId();
                }
                $this->updateCustomerPassword($customerId, $row['password']);
                $this->logger->log('.');
            }
            $this->logger->log(PHP_EOL);
        }
    }

    /**
     * @return array
     */
    protected function getDefaultCustomerProfile()
    {
        if (!$this->customerDataProfile) {
            $this->customerDataProfile = [
                'website_id' => $this->storeManager->getWebsiteId(),
                'group_id' => $this->storeManager->getGroupId(),
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
        }
        return $this->customerDataProfile;
    }

    /**
     * @return array
     */
    protected function getDefaultCustomerAddress()
    {
        if (!$this->customerDataAddress) {
            $this->customerDataAddress = [
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
        }
        return $this->customerDataAddress;
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

    /**
     * @param string $customerId
     * @param string $password
     * @return void
     */
    protected function updateCustomerPassword($customerId, $password)
    {
        $customerAccountData = $this->customerAccount->getCustomer($customerId);
        $customerModel = $this->customerFactory->create()->setWebsiteId($customerAccountData->getWebsiteId());
        $customerModel->loadByEmail($customerAccountData->getEmail())->setPassword($password)->save();
    }
}
