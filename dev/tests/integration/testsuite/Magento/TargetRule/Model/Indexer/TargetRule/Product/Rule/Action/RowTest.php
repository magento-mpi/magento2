<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action;

class RowTest extends \Magento\TestFramework\Indexer\TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_rule;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    protected function setUp()
    {
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor'
        );
        $this->_rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Rule'
        );
        $this->_product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     * @magentoDataFixture Magento/TargetRule/_files/related.php
     */
    public function testReindexRow()
    {
        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $this->_product->setId(
            4
        )->setTypeId(
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
        )->setAttributeSetId(
            4
        )->setWebsiteIds(
            [1]
        )->setSku(
            'simple_product_4'
        )->setName(
            'Simple Product 4 Name'
        )->setDescription(
            'Simple Product 4 Full Description'
        )->setShortDescription(
            'Simple Product 4 Short Description'
        )->setPrice(
            1000
        )->setTaxClassId(
            2
        )->setStockData(
            ['use_config_manage_stock' => 1, 'qty' => 24, 'is_in_stock' => 1]
        )->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
        )->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        )->save();

        $this->_rule->load(1);
        $this->assertEquals(3, count($this->_rule->getMatchingProductIds()));
    }
}
