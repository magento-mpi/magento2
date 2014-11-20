<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\Item;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    protected $_collection = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_collection = $this->objectManager->create('Magento\GiftRegistry\Model\Resource\Item\Collection');
    }

    public function testAddProductFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addProductFilter(0));
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->_collection->addProductFilter(99);
        $where = $select->getPart(\Zend_Db_Select::WHERE);
        $this->assertArrayHasKey(0, $where);
        $this->assertContains('product_id', $where[0]);
        $this->assertContains(99, $where[0]);
    }

    public function testAddItemFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addItemFilter(99));
        $this->_collection->addItemFilter(array(100, 101));
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Sitem_id%S = %S99%S)%SAND%S(%Sitem_id%S IN(%S100%S,%S101%S))%A',
            (string)$select
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture Magento/GiftRegistry/_files/resource_item_collection.php
     */
    public function testGiftCollection()
    {
        $gr = $this->objectManager->get('Magento\Framework\Registry')->registry('test_gift_registry');
        $product = $this->objectManager->get('Magento\Framework\Registry')->registry('test_product');

        $collection = $this->objectManager->create('Magento\GiftRegistry\Model\Resource\Item\Collection');
        $collection->addRegistryFilter($gr->getId())->addWebsiteFilter();

        $this->assertTrue($collection->getSize() > 0);

        $relation = $this->objectManager->create('Magento\Catalog\Model\Product\Website');
        $relation->removeProducts(array(1), array($product->getId()));

        $collection = $this->objectManager->create(
            'Magento\GiftRegistry\Model\Resource\Item\Collection'
        )->addRegistryFilter(
            $gr->getId()
        )->addWebsiteFilter();

        $this->assertTrue($collection->getSize() == 0);
    }
}
