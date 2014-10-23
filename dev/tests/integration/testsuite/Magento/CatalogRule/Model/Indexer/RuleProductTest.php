<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer;

use Magento\TestFramework\Helper\Bootstrap;

class RuleProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\IndexBuilder
     */
    protected $indexBuilder;

    /**
     * @var \Magento\CatalogRule\Model\Resource\Rule
     */
    protected $resourceRule;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    protected function setUp()
    {
        $this->ruleFactory = Bootstrap::getObjectManager()->get('Magento\CatalogRule\Model\RuleFactory');
        $this->indexBuilder = Bootstrap::getObjectManager()->get('Magento\CatalogRule\Model\Indexer\IndexBuilder');
        $this->resourceRule = Bootstrap::getObjectManager()->get('Magento\CatalogRule\Model\Resource\Rule');
        $this->product = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAfterRuleCreationIfProductIsSuitable()
    {
        $this->product->load(1)
            ->setData('test_attribute', 'test_attribute_value')
            ->setPrice(100)
            ->save();

        $this->assertFalse($this->resourceRule->getRulePrice(true, 1, 1, $this->product->getId()));

        $this->saveRule();
        $this->indexBuilder->reindexFull();

        $this->assertEquals(98, $this->resourceRule->getRulePrice(true, 1, 1, $this->product->getId()));
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/CatalogRule/_files/attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testReindexAfterRuleCreationIfProductIsNotSuitable()
    {
        $this->product->load(1)
            ->setData('test_attribute', 'NO_test_attribute_value')
            ->setPrice(100)
            ->save();

        $this->assertFalse($this->resourceRule->getRulePrice(true, 1, 1, $this->product->getId()));

        $this->saveRule();
        $this->indexBuilder->reindexFull();

        $this->assertFalse($this->resourceRule->getRulePrice(true, 1, 1, $this->product->getId()));
    }

    protected function saveRule()
    {
        /** @var \Magento\CatalogRule\Model\Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->loadPost([
            'name' => 'test_rule',
            'is_active' => '1',
            'website_ids' => [1],
            'customer_group_ids' => [0, 1],
            'discount_amount' => 2,
            'simple_action' => 'by_percent',
            'from_date' => '',
            'to_date' => '',
            'conditions' => [
                '1' => [
                    'type' => 'Magento\CatalogRule\Model\Rule\Condition\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ],
                '1--1' => [
                    'type' => 'Magento\CatalogRule\Model\Rule\Condition\Product',
                    'attribute' => 'test_attribute',
                    'operator' => '==',
                    'value' => 'test_attribute_value',
                ],
            ],
        ]);
        $rule->save();
    }
}
