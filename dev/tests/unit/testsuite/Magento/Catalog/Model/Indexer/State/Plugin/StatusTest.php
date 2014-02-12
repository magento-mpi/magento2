<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\State\Plugin;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterSetStatus()
    {
        $testableIndex  = \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID;
        $changedIndex   = \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID;
        $testableStatus = 'testable_status';

        $stateFactory = $this->getMockBuilder('Magento\Indexer\Model\Indexer\StateFactory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $testableState = $this->getMockBuilder('Magento\Indexer\Model\Indexer\State')
            ->setMethods(array('getIndexerId', 'getStatus', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $testableState->expects($this->exactly(2))
            ->method('getIndexerId')
            ->will($this->returnValue($testableIndex));

        $testableState->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($testableStatus));

        $state = $this->getMockBuilder('Magento\Indexer\Model\Indexer\State')
            ->setMethods(array('setData', 'load', 'save', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $state->expects($this->once())
            ->method('load')
            ->with(
                $this->logicalOr(
                    $this->equalTo('indexer_id'),
                    $this->equalTo($changedIndex)
                )
            )
            ->will($this->returnSelf());

        $state->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $state->expects($this->once())
            ->method('setData')
            ->with(
                $this->logicalOr(
                    $this->equalTo('status'),
                    $this->equalTo($testableStatus)
                )
            )
            ->will($this->returnSelf());

        $stateFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($state));

        $model = new \Magento\Catalog\Model\Indexer\State\Plugin\Status($stateFactory);
        $this->assertInstanceOf('\Magento\Indexer\Model\Indexer\State', $model->afterSetStatus($testableState));
    }
}
