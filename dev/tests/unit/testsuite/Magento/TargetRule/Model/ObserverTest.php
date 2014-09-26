<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    protected $_observer;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleProcessorMock;

    /**
     * @var \Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productRuleIndexer;

    public function setUp()
    {
        $this->_productRuleIndexer = $this->getMock('\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule',
            [],
            [],
            '',
            false
        );

        $this->_productRuleProcessorMock = $this->getMock(
            '\Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor',
            [],
            [],
            '',
            false
        );

        $this->_observer = (new ObjectManager($this))->getObject('\Magento\TargetRule\Model\Observer', [
            'productRuleIndexerProcessor' => $this->_productRuleProcessorMock,
            'productRuleIndexer' => $this->_productRuleIndexer,
        ]);
    }

    public function testCatalogProductSaveCommitAfter()
    {
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        $productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_productRuleProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with(1);

        $this->_observer->catalogProductSaveCommitAfter($observerMock);
    }

    public function testCatalogProductDeleteCommitAfter()
    {
        $productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getId', '__sleep', '__wakeup'],
            [],
            '',
            false
        );
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct'], [], '', false);

        $eventMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        $productMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_productRuleIndexer->expects($this->once())
            ->method('cleanAfterProductDelete')
            ->with(1);

        $this->_observer->catalogProductDeleteCommitAfter($observerMock);
    }

    public function testCoreConfigSaveCommitAfter()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getDataObject'], [], '', false);
        $dataObject = $this->getMock('\Magento\Framework\Object', ['getPath', 'isValueChanged'], [], '', false);

        $dataObject->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('customer/magento_customersegment/is_enabled'));

        $dataObject->expects($this->once())
            ->method('isValueChanged')
            ->will($this->returnValue(true));

        $observerMock->expects($this->exactly(2))
            ->method('getDataObject')
            ->will($this->returnValue($dataObject));

        $this->_productRuleProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_observer->coreConfigSaveCommitAfter($observerMock);
    }

    public function testCoreConfigSaveCommitAfterNoChanges()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getDataObject'], [], '', false);
        $dataObject = $this->getMock('\Magento\Framework\Object', ['getPath', 'isValueChanged'], [], '', false);
        $dataObject->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('customer/magento_customersegment/is_enabled'));

        $dataObject->expects($this->once())
            ->method('isValueChanged')
            ->will($this->returnValue(false));

        $observerMock->expects($this->exactly(2))
            ->method('getDataObject')
            ->will($this->returnValue($dataObject));

        $this->_productRuleProcessorMock->expects($this->never())
            ->method('markIndexerAsInvalid');

        $this->_observer->coreConfigSaveCommitAfter($observerMock);
    }

    public function testPrepareTargetRuleSave()
    {
        $vars = ['targetrule_rule_based_positions', 'tgtr_position_behavior'];
        $prefixes = ['related_', 'upsell_', 'crosssell_'];
        $dataKeys = [];
        /** @var \Magento\Framework\Object $productMock */
        $productMock = (new ObjectManager($this))->getObject('Magento\Framework\Object');
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventMock = $this->getMock('\Magento\Framework\Event', ['getProduct'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));

        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        foreach ($vars as $var) {
            foreach ($prefixes as $prefix) {
                $dataKeys[] = $prefix . $var;
                $productMock->setData($prefix . $var . '_default', 1);
                $productMock->setData($prefix . $var, rand(0, 111));
            }
        }

        $this->_observer->prepareTargetRuleSave($observerMock);

        foreach ($dataKeys as $key) {
            $this->assertNull($productMock->getData($key));
        }
    }
}
