<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Indexer\Eav;
/**
 * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
 */
class SourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source
     */
    protected $source;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $productResource;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->source = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source'
        );

        /** @var \Magento\Catalog\Model\Resource\Product $productResource */
        $this->productResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Resource\Product'
        );
    }

    /**
     *  Test reindex for configurable product with both disabled and enabled variations.
     */
    public function testReindexEntitiesForConfigurableProduct()
    {
        /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attr **/
        $attr = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config')
           ->getAttribute('catalog_product', 'test_configurable');
        $attr->setIsFilterable(1)->save();

        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection $options **/
        $options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection'
        );
        $options->setAttributeFilter($attr->getId())->load();
        $optionIds = $options->getAllIds();

        $adapter = $this->productResource->getReadConnection();
        $select = $adapter->select()->from($this->productResource->getTable('catalog_product_index_eav'))
            ->where('entity_id = ?', 1)
            ->where('attribute_id = ?', $attr->getId())
            ->where('value IN (?)', $optionIds);

        $result = $adapter->fetchAll($select);
//        $this->assertCount(2, $result);

        /** @var \Magento\Catalog\Model\Product $product1 **/
        $product1 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product');
        $product1 = $product1->load($optionIds[0] * 10);
        $product1->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)->save();

        /** @var \Magento\Catalog\Model\Product $product2 **/
        $product2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product');
        $product2 = $product2->load($optionIds[1] * 10);
        $product2->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)->save();

        $result = $adapter->fetchAll($select);
        $this->assertCount(0, $result);
    }
}
