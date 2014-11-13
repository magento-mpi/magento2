<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\GiftRegistry\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Class GiftRegistry
 *
 * @package Magento\Tools\SampleData\Module\GiftRegistry\Setup
 */
class GiftRegistry implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\GiftRegistry\Model\Entity
     */
    protected $giftRegistryFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory,
     * @param \Magento\Directory\Model\CountryFactory $countryFactory,
     * @param \Magento\GiftRegistry\Model\EntityFactory $giftRegistryFactory,
     * @param \Magento\Customer\Model\AddressFactory $addressFactory,
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory,
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
     * @param \Magento\Catalog\Model\ProductFactory $productFactory,
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\GiftRegistry\Model\EntityFactory $giftRegistryFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->countryFactory = $countryFactory;
        $this->giftRegistryFactory = $giftRegistryFactory;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->dateFactory = $dateFactory;
        $this->productFactory = $productFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing Gift Registry' . PHP_EOL;
        $data = $this->generateData();
        $giftRegistry = $this->giftRegistryFactory->create();
        $address = $this->addressFactory->create();
        $address->setData($data['address']);
        $giftRegistry->setTypeById($data['type_id']);
        $giftRegistry->importData($data);
        $giftRegistry->addData(
            [
                'customer_id' => $data['customer_id'],
                'website_id' => 1,
                'url_key' => $giftRegistry->getGenerateKeyId(),
                'created_at' => $this->dateFactory->create()->date(),
                'is_add_action' => true
            ]
        );
        $giftRegistry->importAddress($address);
        $validationPassed = $giftRegistry->validate();
        if ($validationPassed) {
            $giftRegistry->save();
            foreach($data['items'] as $productId) {
                $item = $this->itemFactory->create();
                $item->setEntityId($giftRegistry->getId())
                    ->setProductId($productId)
                    ->setQty(1)
                    ->save();
            }
        }
        echo '.';
        echo PHP_EOL;
    }

    /**
     * @return array
     */
    private function generateData()
    {
        $fixtureFile = 'Customer/customer_profile.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $customerData) {}
        $fixtureFile = 'GiftRegistry/gift_registry.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $giftRegistryData) {}
        $giftRegistryData['sku'] = explode("\n", $giftRegistryData['sku']);
        $productIds = $this->productSkuToId($giftRegistryData['sku']);
        $customerId = $this->customerFactory->create()
            ->setWebsiteId(1)
            ->loadByEmail($customerData['email'])
            ->getId();
        return [
            'customer_id' => $customerId,
            'type_id' => 1,
            'title' => $giftRegistryData['title'],
            'message' =>  $giftRegistryData['message'],
            'is_public' => 1,
            'is_active' => 1,
            'event_country' => $customerData['country_id'],
            'event_country_region' => $this->getRegionId($customerData),
            'event_country_region_text' => '',
            'event_date' => date('Y-m-d'),
            'address' =>
                [
                    'firstname' => $customerData['firstname'],
                    'lastname' => $customerData['lastname'],
                    'company' => '',
                    'street' => $customerData['street'],
                    'city' => $customerData['city'],
                    'region_id' => $this->getRegionId($customerData),
                    'region' => $customerData['region'],
                    'postcode' => $customerData['postcode'],
                    'country_id' => $customerData['country_id'],
                    'telephone' => $customerData['telephone'],
                    'fax' => '',
                ],
            'items' => $productIds,
        ];
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
     * @param array $skus
     * @return array
     */
    protected function productSkuToId(array $skus)
    {
        $ids = [];
        foreach ($skus as $sku) {
            $id = $this->productFactory->create()->getIdBySku($sku);
            if ($id) {
                $ids[] = $id;
            }
        }
        return $ids;
    }
}
