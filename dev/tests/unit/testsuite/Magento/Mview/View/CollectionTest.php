<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\View;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDataAndGetViewsByStateMode()
    {
        $indexerIdOne = 'first_indexer_id';
        $indexerIdSecond = 'second_indexer_id';

        $entityFactory = $this->getMockBuilder('Magento\Data\Collection\EntityFactoryInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $config = $this->getMockBuilder('Magento\Mview\ConfigInterface')
            ->getMock();

        $statesFactory = $this->getMockBuilder('Magento\Mview\View\State\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $states = $this->getMockBuilder('Magento\Mview\View\State\Collection')
            ->setMethods(array('getItems'))
            ->disableOriginalConstructor()
            ->getMock();

        $state = $this->getMockForAbstractClass(
            'Magento\Mview\View\StateInterface', [], '', false, false, true,
            ['getViewId', 'getMode', '__wakeup']
        );

        $state->expects($this->any())
            ->method('getViewId')
            ->will($this->returnValue('second_indexer_id'));

        $state->expects($this->any())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Mview\View\StateInterface::MODE_DISABLED));

        $view = $this->getMockForAbstractClass(
            'Magento\Mview\ViewInterface', [], '', false, false, true,
            ['load', 'setState', 'getState', '__wakeup']
        );

        $view->expects($this->once())
            ->method('setState')
            ->with($state);
        $view->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($state));
        $view->expects($this->any())
            ->method('load')
            ->with($this->logicalOr($indexerIdOne, $indexerIdSecond));

        $entityFactory->expects($this->any())
            ->method('create')
            ->with('Magento\Mview\ViewInterface')
            ->will($this->returnValue($view));

        $statesFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($states));

        $config->expects($this->once())
            ->method('getViews')
            ->will($this->returnValue(array($indexerIdOne => 1, $indexerIdSecond => 2)));

        $states->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($state)));

        $collection = new \Magento\Mview\View\Collection($entityFactory, $config, $statesFactory);
        $this->assertInstanceOf('Magento\Mview\View\Collection', $collection->loadData());

        $views = $collection->getViewsByStateMode(\Magento\Mview\View\StateInterface::MODE_DISABLED);
        $this->assertCount(2, $views);
        $this->assertInstanceOf('Magento\Mview\ViewInterface', $views[0]);
        $this->assertInstanceOf('Magento\Mview\ViewInterface', $views[1]);

        $views = $collection->getViewsByStateMode(\Magento\Mview\View\StateInterface::MODE_ENABLED);
        $this->assertCount(0, $views);
    }
}
