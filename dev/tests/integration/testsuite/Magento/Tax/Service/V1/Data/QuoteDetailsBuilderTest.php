<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;


class QuoteDetailsBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\ObjectManager */
    private $objectManager;

    /** @var QuoteDetailsBuilder */
    private $builder;

    /* @var QuoteDetails\ItemBuilder */
    private $itemBuilder;

    /* @var \Magento\Customer\Service\V1\Data\AddressBuilder */
    private $addressBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('Magento\Tax\Service\V1\Data\QuoteDetailsBuilder');
        $this->itemBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder');
        $this->addressBuilder = $this->objectManager->create('\Magento\Customer\Service\V1\Data\AddressBuilder');
    }

    /**
     * @param array $dataArray
     * @param array $items
     * @dataProvider createDataProvider
     */
    public function testCreateWithPopulateWithArray($dataArray, $items = [])
    {
        if (!empty($items)) {
            $dataArray[QuoteDetails::KEY_ITEMS] = $items;
        }
        $taxRate = $this->builder->populateWithArray($dataArray)->create();
        $taxRate2 = $this->generateQuoteDetailsWithSetters($dataArray);

        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\QuoteDetails', $taxRate);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\QuoteDetails', $taxRate2);
        $this->assertEquals($taxRate2, $taxRate);
        $this->assertEquals($dataArray, $taxRate->__toArray());
        $this->assertEquals($dataArray, $taxRate2->__toArray());
    }

    /**
     * @param array $dataArray
     * @param array $items
     * @dataProvider createDataProvider
     */
    public function testPopulate($dataArray, $items = [])
    {
        if (!empty($items)) {
            $dataArray[QuoteDetails::KEY_ITEMS] = $items;
        }
        $taxRate = $this->generateQuoteDetailsWithSetters($dataArray);
        $taxRate2 = $this->builder->populate($taxRate)->create();
        $this->assertEquals($taxRate, $taxRate2);
    }

    public function createDataProvider()
    {
        $data = $this->getData()['dataMerged'];
        $items = $data[QuoteDetails::KEY_ITEMS];
        unset($data[QuoteDetails::KEY_ITEMS]);

        return [
            'withEmptyData' => [[],[]],
            'withEmptyQuoteItems' => [$data],
            'withQuoteItems' => [[], $items],
            'withQuoteDetailsAndItems' => [$data, $items]
        ];
    }

    public function testMergeDataObjects()
    {
        $data = $this->getData();
        $taxRate = $this->builder->populateWithArray($data['dataMerged'])->create();
        $taxRate1 = $this->builder->populateWithArray($data['data1'])->create();
        $taxRate2 = $this->builder->populateWithArray($data['data2'])->create();
        $taxRateMerged = $this->builder->mergeDataObjects($taxRate1, $taxRate2);
        $this->assertEquals($taxRate->__toArray(), $taxRateMerged->__toArray());
    }

    public function testMergeDataObjectWithArray()
    {
        $data = $this->getData();

        $taxRate = $this->builder->populateWithArray($data['dataMerged'])->create();
        $taxRate1 = $this->builder->populateWithArray($data['data1'])->create();
        $taxRateMerged = $this->builder->mergeDataObjectWithArray($taxRate1, $data['data2']);
        $this->assertEquals($taxRate->__toArray(), $taxRateMerged->__toArray());
    }

    /**
     * @return array
     */
    protected function getData()
    {
        $addressData = [
            'id' => 14,
            'default_shipping' => true,
            'default_billing' => true,
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
            'region' => ['region_id' => 0, 'region' => 'Texas']
        ];

        $items = [
            [
                'code' => 'item code',
                'type' => 'shipping',
                'row_total' => 14.85,
                'discount_amount' => 2.6
            ],
            [
                'code' => 'another code',
                'type' => 'product',
                'row_total' => 10,
                'discount_amount' => 5
            ]
        ];
        $data = [
            'data1' => [
                QuoteDetails::KEY_BILLING_ADDRESS => $addressData,
                QuoteDetails::KEY_CUSTOMER_TAX_CLASS_ID => 1,
            ],
            'data2' => [
                QuoteDetails::KEY_SHIPPING_ADDRESS => $addressData,
                QuoteDetails::KEY_ITEMS => $items
            ],
            'dataMerged' => [
                QuoteDetails::KEY_BILLING_ADDRESS => $addressData,
                QuoteDetails::KEY_SHIPPING_ADDRESS => $addressData,
                QuoteDetails::KEY_CUSTOMER_TAX_CLASS_ID => 1,
                QuoteDetails::KEY_ITEMS => $items
            ]
        ];

        return $data;
    }

    /**
     * @param array $dataArray
     * @return QuoteDetails
     */
    protected function generateQuoteDetailsWithSetters($dataArray)
    {
        $this->builder->populateWithArray([]);
        if (array_key_exists(QuoteDetails::KEY_CUSTOMER_TAX_CLASS_ID, $dataArray)) {
            $this->builder->setCustomerTaxClassId($dataArray[QuoteDetails::KEY_CUSTOMER_TAX_CLASS_ID]);
        }
        if (array_key_exists(QuoteDetails::KEY_BILLING_ADDRESS, $dataArray)) {
            $this->builder->setShippingAddress(
                $this->addressBuilder->populateWithArray(
                    $dataArray[QuoteDetails::KEY_BILLING_ADDRESS]
                )->create()
            );
        }
        if (array_key_exists(QuoteDetails::KEY_SHIPPING_ADDRESS, $dataArray)) {
            $this->builder->setBillingAddress(
                $this->addressBuilder->populateWithArray(
                    $dataArray[QuoteDetails::KEY_SHIPPING_ADDRESS]
                )->create()
            );
        }
        if (array_key_exists(QuoteDetails::KEY_ITEMS, $dataArray)) {
            $items = [];
            foreach ($dataArray[QuoteDetails::KEY_ITEMS] as $itemArray) {
                $items[] = $this->itemBuilder->populateWithArray($itemArray)->create();
            }
            $this->builder->setItems($items);
        }
        return $this->builder->create();
    }
}
