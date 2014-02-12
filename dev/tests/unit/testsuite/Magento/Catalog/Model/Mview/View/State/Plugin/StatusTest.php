<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Mview\View\State\Plugin;

class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterSetStatus()
    {
        $testableView  = \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID;
        $changedView   = \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID;
        $testableStatus = \Magento\Mview\View\StateInterface::STATUS_SUSPENDED;
        $testableVersion = 'testable_version';

        $changelogFactory = $this->getMockBuilder('Magento\Mview\View\ChangelogInterfaceFactory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $changelog = $this->getMockBuilder('Magento\Mview\View\ChangelogInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $changelog->expects($this->once())
            ->method('setViewId')
            ->with($this->equalTo($changedView))
            ->will($this->returnSelf());

        $changelog->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue($testableVersion));

        $changelogFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($changelog));

        $stateFactory = $this->getMockBuilder('Magento\Mview\View\StateInterfaceFactory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $testableState = $this->getMockBuilder('Magento\Mview\View\StateInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $testableState->expects($this->exactly(2))
            ->method('getViewId')
            ->will($this->returnValue($testableView));

        $testableState->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($testableStatus));

        $state = $this->getMockBuilder('Magento\Mview\View\StateInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $state->expects($this->once())
            ->method('loadByView')
            ->with($this->equalTo($changedView))
            ->will($this->returnSelf());

        $state->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $state->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue('any_different_status'));

        $state->expects($this->once())
            ->method('setVersionId')
            ->with($this->equalTo($testableVersion))
            ->will($this->returnSelf());

        $state->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo($testableStatus))
            ->will($this->returnSelf());

        $stateFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($state));

        $model = new \Magento\Catalog\Model\Mview\View\State\Plugin\Status(
            $stateFactory,
            $changelogFactory
        );
        $this->assertInstanceOf('\Magento\Mview\View\StateInterface', $model->afterSetStatus($testableState));
    }
}
