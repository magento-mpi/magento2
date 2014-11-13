<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data\QuoteDetails;

use Magento\Tax\Service\V1\Data\TaxClassKey;

class ItemBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * TaxClassKey data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder
     */
    private $taxClassKeyBuilder;

    /**
     * Quote Details Item data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    private $quoteDetailsItemBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->taxClassKeyBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\Data\TaxClassKeyBuilder');
        $this->quoteDetailsItemBuilder = $this->objectManager
            ->create('Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder');
    }

    /**
     * @param array $dataArray Array with data for item
     * @dataProvider createDataProvider
     */
    public function testPopulateWithArray($dataArray)
    {
        $itemFromPopulate = $this->quoteDetailsItemBuilder->populateWithArray($dataArray)->create();
        $itemFromSetters = $this->generateItemWithSetters($dataArray);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\QuoteDetails\Item', $itemFromPopulate);
        $this->assertInstanceOf('\Magento\Tax\Service\V1\Data\QuoteDetails\Item', $itemFromSetters);
        $this->assertEquals($itemFromSetters, $itemFromPopulate);
        $this->assertEquals($dataArray, $itemFromPopulate->__toArray());
        $this->assertEquals($dataArray, $itemFromSetters->__toArray());
    }

    /**
     * @param array $dataArray Array with data for item
     * @dataProvider createDataProvider
     */
    public function testPopulate($dataArray)
    {
        $itemFromSetters = $this->generateItemWithSetters($dataArray);
        $itemFromPopulate = $this->quoteDetailsItemBuilder->populate($itemFromSetters)->create();
        $this->assertEquals($itemFromSetters, $itemFromPopulate);
    }

    public function createDataProvider()
    {
        return[
            'empty' => [[]],
            'case1' => [$this->getData()['data1']],
            'case2' => [$this->getData()['data2']],
        ];
    }

    public function testMergeDataObjects()
    {
        $data = $this->getData();
        $itemExpected = $this->quoteDetailsItemBuilder->populateWithArray($data['dataMerged'])->create();
        $itemSomeFields = $this->quoteDetailsItemBuilder->populateWithArray($data['data1'])->create();
        $itemMoreFields = $this->quoteDetailsItemBuilder->populateWithArray($data['data2'])->create();
        $itemMerged = $this->quoteDetailsItemBuilder->mergeDataObjects($itemSomeFields, $itemMoreFields)
            ->create();
        $this->assertEquals($itemExpected->__toArray(), $itemMerged->__toArray());
    }

    public function testMergeDataObjectsWithArray()
    {
        $data = $this->getData();
        $itemExpected = $this->quoteDetailsItemBuilder->populateWithArray($data['dataMerged'])->create();
        $itemSomeFields = $this->quoteDetailsItemBuilder->populateWithArray($data['data1'])->create();
        $itemMerged = $this->quoteDetailsItemBuilder->mergeDataObjectWithArray($itemSomeFields, $data['data2'])
            ->create();
        $this->assertEquals($itemExpected->__toArray(), $itemMerged->__toArray());
    }

    /**
     * Creates a QuoteDetails item data object by calling setters.
     *
     * @param array $dataArray
     * @return Item
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function generateItemWithSetters($dataArray)
    {
        $this->quoteDetailsItemBuilder->populateWithArray([]);
        if (array_key_exists(Item::KEY_CODE, $dataArray)) {
            $this->quoteDetailsItemBuilder->setCode($dataArray[Item::KEY_CODE]);
        }
        if (array_key_exists(Item::KEY_TYPE, $dataArray)) {
            $this->quoteDetailsItemBuilder->setType($dataArray[Item::KEY_TYPE]);
        }
        if (array_key_exists(Item::KEY_TAX_CLASS_KEY, $dataArray)) {
            $this->quoteDetailsItemBuilder->setTaxClassKey(
                $this->taxClassKeyBuilder->setType($dataArray[Item::KEY_TAX_CLASS_KEY][TaxClassKey::KEY_TYPE])
                    ->setValue($dataArray[Item::KEY_TAX_CLASS_KEY][TaxClassKey::KEY_VALUE])
                    ->create()
            );
        }
        if (array_key_exists(Item::KEY_DISCOUNT_AMOUNT, $dataArray)) {
            $this->quoteDetailsItemBuilder->setDiscountAmount($dataArray[Item::KEY_DISCOUNT_AMOUNT]);
        }
        if (array_key_exists(Item::KEY_QUANTITY, $dataArray)) {
            $this->quoteDetailsItemBuilder->setQuantity($dataArray[Item::KEY_QUANTITY]);
        }
        if (array_key_exists(Item::KEY_PARENT_CODE, $dataArray)) {
            $this->quoteDetailsItemBuilder->setParentCode($dataArray[Item::KEY_PARENT_CODE]);
        }
        if (array_key_exists(Item::KEY_SHORT_DESCRIPTION, $dataArray)) {
            $this->quoteDetailsItemBuilder->setShortDescription($dataArray[Item::KEY_SHORT_DESCRIPTION]);
        }
        if (array_key_exists(Item::KEY_UNIT_PRICE, $dataArray)) {
            $this->quoteDetailsItemBuilder->setUnitPrice($dataArray[Item::KEY_UNIT_PRICE]);
        }
        if (array_key_exists(Item::KEY_TAX_INCLUDED, $dataArray)) {
            $this->quoteDetailsItemBuilder->setTaxIncluded($dataArray[Item::KEY_TAX_INCLUDED]);
        }
        return $this->quoteDetailsItemBuilder->create();
    }

    /**
     * Get item data
     *
     * @return array
     */
    protected function getData()
    {
        $data1 = [
            Item::KEY_CODE => 'item code',
            Item::KEY_TYPE => 'shipping',
            Item::KEY_TAX_CLASS_KEY => [
                TaxClassKey::KEY_TYPE => TaxClassKey::TYPE_ID,
                TaxClassKey::KEY_VALUE => 1,
            ],
            Item::KEY_UNIT_PRICE => 10,
            Item::KEY_DISCOUNT_AMOUNT => 2.6,
        ];

        $data2 = [
            Item::KEY_CODE => 'another code',
            Item::KEY_TYPE => 'product',
            Item::KEY_DISCOUNT_AMOUNT => 5,
            Item::KEY_QUANTITY => 2,
            Item::KEY_TAX_INCLUDED => false,
            Item::KEY_SHORT_DESCRIPTION => 'product',
            Item::KEY_PARENT_CODE => 'parent',
            Item::KEY_TAX_CLASS_KEY => [
                TaxClassKey::KEY_TYPE => TaxClassKey::TYPE_NAME,
                TaxClassKey::KEY_VALUE => 'tax class name',
            ],
        ];

        $data = [
            'data1' => $data1,
            'data2' => $data2,
            'dataMerged' => [
                Item::KEY_CODE => 'another code',
                Item::KEY_TYPE => 'product',
                Item::KEY_DISCOUNT_AMOUNT => 5,
                Item::KEY_QUANTITY => 2,
                Item::KEY_TAX_INCLUDED => false,
                Item::KEY_SHORT_DESCRIPTION => 'product',
                Item::KEY_PARENT_CODE => 'parent',
                Item::KEY_UNIT_PRICE => 10,
                Item::KEY_TAX_CLASS_KEY => [
                    TaxClassKey::KEY_TYPE => TaxClassKey::TYPE_NAME,
                    TaxClassKey::KEY_VALUE => 'tax class name',
                ],
            ]
        ];

        return $data;
    }
}
