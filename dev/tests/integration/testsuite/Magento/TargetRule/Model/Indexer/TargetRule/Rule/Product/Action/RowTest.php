<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action;

class RowTest extends \Magento\TestFramework\Indexer\TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\TargetRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    protected function setUp()
    {
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor'
        );
        $this->_ruleFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\TargetRule\Model\RuleFactory'
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

        $data = [
            'name' => 'Target Rule',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => ['0' => ''],
        ];
        $rule = $this->_ruleFactory->create();
        $rule->loadPost($data);
        $rule->save();

        $this->assertEquals(2, count($rule->getMatchingProductIds()));
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

        $data = [
            'name' => 'related',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => ['0' => ''],
            'conditions' => [
                '1' => [
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ],
                '1--1' => [
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
                    'attribute' => 'category_ids',
                    'operator' => '()',
                    'value' => '11',
                ],
            ],
        ];
        $rule = $this->_ruleFactory->create();
        $rule->loadPost($data);
        $rule->save();

        $testSelect = $rule->getResource()->getReadConnection()->select()->from(
            $rule->getResource()->getTable('magento_targetrule_product'),
            'product_id'
        )->where(
            'rule_id = ?', $rule->getId()
        );

        $this->assertEquals([3, 4], $rule->getResource()->getReadConnection()->fetchCol($testSelect));

        $data = [
            'name' => 'related',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => ['0' => ''],
            'conditions' => [
                '1' => [
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ],
                '1--1' => [
                    'type' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => '5',
                ],
            ],
        ];
        $rule = $this->_ruleFactory->create();
        $rule->loadPost($data);
        $rule->save();

        $testSelect = $rule->getResource()->getReadConnection()->select()->from(
            $rule->getResource()->getTable('magento_targetrule_product'),
            'product_id'
        )->where(
            'rule_id = ?', $rule->getId()
        );

        $this->assertEquals([2], $rule->getResource()->getReadConnection()->fetchCol($testSelect));
    }
}
