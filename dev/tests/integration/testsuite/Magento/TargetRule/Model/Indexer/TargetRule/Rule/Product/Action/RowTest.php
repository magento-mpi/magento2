<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action;

class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $_rule;

    protected function setUp()
    {
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor'
        );
        $this->_rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Rule'
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testReindexRow()
    {
        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $data = array(
            'name' => 'Target Rule',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => array('0' => '')
        );
        $this->_rule->loadPost($data);
        $this->_rule->save();

        $this->assertEquals(2, count($this->_rule->getMatchingProductIds()));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testReindexRowByCategories()
    {
        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $data = array(
            'name' => 'related',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => array('0' => ''),
            'conditions' => array(
                '1' => array(
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ),
                '1--1' => array(
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => '11'
                )
            )
        );
        $this->_rule->loadPost($data);
        $this->_rule->save();
        $this->_rule->prepareMatchingProducts();

        $this->assertEquals(2, count($this->_rule->getMatchingProductIds()));

        $data = array(
            'name' => 'related',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => array('0' => ''),
            'conditions' => array(
                '1' => array(
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                ),
                '1--1' => array(
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => '5'
                )
            )
        );
        $this->_rule->loadPost($data);
        $this->_rule->save();
        $this->_rule->prepareMatchingProducts();

        $this->assertEquals(1, count($this->_rule->getMatchingProductIds()));
    }
}
