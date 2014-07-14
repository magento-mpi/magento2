<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product
     */
    protected $_productIndexer;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleProcessor;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductIndexerFull;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleProductProcessor;

    public function setUp()
    {
        $this->_ruleProductProcessor = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor',
            [],
            [],
            '',
            false
        );
        $this->_productRuleProcessor = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );
        $this->_ruleProductIndexerFull = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Action\Full',
            [],
            [],
            '',
            false
        );
        $ruleProductIndexerRows = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Rows',
            [],
            [],
            '',
            false
        );
        $ruleProductIndexerRow = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action\Row',
            [],
            [],
            '',
            false
        );
        $this->_productIndexer = new \Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product(
            $ruleProductIndexerRow,
            $ruleProductIndexerRows,
            $this->_ruleProductIndexerFull,
            $this->_productRuleProcessor,
            $this->_ruleProductProcessor
        );
    }

    public function testFullReindexIfNotExecutedRelatedIndexer()
    {
        $this->_ruleProductIndexerFull->expects($this->once())
            ->method('execute');
        $this->_productRuleProcessor->expects($this->once())
            ->method('isFullReindexPassed')
            ->will($this->returnValue(false));
        $this->_ruleProductProcessor->expects($this->once())
            ->method('setFullReindexPassed');
        $this->_productIndexer->executeFull();
    }

    public function testFullReindexIfRelatedIndexerPassed()
    {
        $this->_ruleProductIndexerFull->expects($this->never())
            ->method('execute');
        $this->_productRuleProcessor->expects($this->once())
            ->method('isFullReindexPassed')
            ->will($this->returnValue(true));
        $this->_ruleProductProcessor->expects($this->never())
            ->method('setFullReindexPassed');
        $this->_productIndexer->executeFull();
    }
}
