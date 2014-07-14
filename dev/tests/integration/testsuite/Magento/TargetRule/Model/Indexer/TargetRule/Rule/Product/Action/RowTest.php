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

/**
 * Full reindex Test
 */
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
            'name' => 'rule',
            'is_active' => '1',
            'apply_to' => 1,
            'use_customer_segment' => '0',
            'customer_segment_ids' => array('0' => '')
        );
        $this->_rule->loadPost($data);
        $this->_rule->save();

        $this->assertEquals(2, count($this->_rule->getMatchingProductIds()));
    }
}
