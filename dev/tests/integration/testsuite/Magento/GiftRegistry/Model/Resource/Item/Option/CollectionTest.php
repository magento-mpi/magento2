<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\Item\Option;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddProductFilter()
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\GiftRegistry\Model\Resource\Item\Option\Collection'
        );
        $select = $collection->getSelect();
        $this->assertSame([], $select->getPart(\Zend_Db_Select::WHERE));

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setId(4);
        $collection->addProductFilter(1)->addProductFilter([2, 3])->addProductFilter($product);
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Aproduct_id%A = %S1%S)%SAND%S(%Aproduct_id%A IN(%S2%S,%S3%S))%SAND%S(%Aproduct_id%A = %S4%S)%A',
            (string)$select
        );
    }

    public function testAddProductFilterZero()
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\GiftRegistry\Model\Resource\Item\Option\Collection'
        );
        $collection->addProductFilter(0);
        $this->assertSame([], $collection->getSelect()->getPart(\Zend_Db_Select::WHERE));
        foreach ($collection as $item) {
            $this->fail("Unexpected item in collection: {$item->getId()}");
        }
    }
}
