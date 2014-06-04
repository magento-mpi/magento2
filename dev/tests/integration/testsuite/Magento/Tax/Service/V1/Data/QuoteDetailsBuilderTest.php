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

    /* @var QuoteDetails\Item */
    private $item;

    /* @var \Magento\Customer\Service\V1\Data\Address */
    private $address;

    /** @var \Magento\Customer\Service\V1\Data\Customer */
    private $customer;

    /**  @var \Magento\Customer\Service\V1\Data\Customer */
    private $customerGroup;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->builder = $this->objectManager->create('Magento\Tax\Service\V1\Data\QuoteDetailsBuilder');
        $this->itemBuilder = $this->objectManager->create('Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder');
        $this->item = $this->itemBuilder->create();
        $addressBuilder = $this->objectManager->create('\Magento\Customer\Service\V1\Data\AddressBuilder');
        $this->address = $addressBuilder->create();
        $customerBuilder = $this->objectManager->create('\Magento\Customer\Service\V1\Data\CustomerBuilder');
        $this->customer = $customerBuilder->create();
        $customerGroup = $this->objectManager->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $this->customerGroup = $customerGroup->create();
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
        $data = [
            QuoteDetails::KEY_BILLING_ADDRESS => $this->address,
            QuoteDetails::KEY_SHIPPING_ADDRESS => $this->address,
            QuoteDetails::KEY_TAX_CLASS_ID => 1,
            QuoteDetails::KEY_CUSTOMER => $this->customer,
            QuoteDetails::KEY_CUSTOMER_GROUP => $this->customerGroup,
        ];

        return [
            'withEmptyData' => [[], [[]]],
            'withEmptyQuoteItems' => [$data],
            'withQuoteItems' => [[], [$this->item]],
            'withQuoteDetailsAndItems' => [$data, [$this->item]]
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
        $data = [
            'data1' => [
                QuoteDetails::KEY_BILLING_ADDRESS => $this->address,
                QuoteDetails::KEY_TAX_CLASS_ID => 1,
                QuoteDetails::KEY_CUSTOMER_GROUP => $this->customerGroup,
            ],
            'data2' => [
                QuoteDetails::KEY_SHIPPING_ADDRESS => $this->address,
                QuoteDetails::KEY_CUSTOMER => $this->customer,
                QuoteDetails::KEY_ITEMS => [$this->item]
            ],
            'dataMerged' => [
                QuoteDetails::KEY_BILLING_ADDRESS => $this->address,
                QuoteDetails::KEY_SHIPPING_ADDRESS => $this->address,
                QuoteDetails::KEY_TAX_CLASS_ID => 1,
                QuoteDetails::KEY_CUSTOMER => $this->customer,
                QuoteDetails::KEY_CUSTOMER_GROUP => $this->customerGroup,
                QuoteDetails::KEY_ITEMS => [$this->item]
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
        if (array_key_exists(QuoteDetails::KEY_BILLING_ADDRESS, $dataArray)) {
            $this->builder->setBillingAddress($dataArray[QuoteDetails::KEY_BILLING_ADDRESS]);
        }
        if (array_key_exists(QuoteDetails::KEY_SHIPPING_ADDRESS, $dataArray)) {
            $this->builder->setShippingAddress($dataArray[QuoteDetails::KEY_SHIPPING_ADDRESS]);
        }
        if (array_key_exists(QuoteDetails::KEY_TAX_CLASS_ID, $dataArray)) {
            $this->builder->setTaxClassId($dataArray[QuoteDetails::KEY_TAX_CLASS_ID]);
        }
        if (array_key_exists(QuoteDetails::KEY_CUSTOMER, $dataArray)) {
            $this->builder->setCustomer($dataArray[QuoteDetails::KEY_CUSTOMER]);
        }
        if (array_key_exists(QuoteDetails::KEY_CUSTOMER_GROUP, $dataArray)) {
            $this->builder->setCustomerGroup($dataArray[QuoteDetails::KEY_CUSTOMER_GROUP]);
        }
        if (array_key_exists(QuoteDetails::KEY_ITEMS, $dataArray)) {
            $this->builder->setItems($dataArray[QuoteDetails::KEY_ITEMS]);
        }
        return $this->builder->create();
    }
}
