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
    public function testLoadData()
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

        $state = $this->getMockBuilder('Magento\Mview\View\State')
            ->setMethods(array('getViewId', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $state->expects($this->any())
            ->method('getViewId')
            ->will($this->returnValue('second_indexer_id'));

        $indexer = $this->getMockBuilder('Magento\Object')
            ->setMethods(array('load', 'setState'))
            ->disableOriginalConstructor()
            ->getMock();

        $indexer->expects($this->once())
            ->method('setState')
            ->with($state);
        $indexer->expects($this->any())
            ->method('load')
            ->with($this->logicalOr($indexerIdOne, $indexerIdSecond));

        $entityFactory->expects($this->any())
            ->method('create')
            ->with('Magento\Mview\ViewInterface')
            ->will($this->returnValue($indexer));

        $statesFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($states));

        $config->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue(array($indexerIdOne => 1, $indexerIdSecond => 2)));

        $states->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($state)));

        $collection = new \Magento\Mview\View\Collection($entityFactory, $config, $statesFactory);
        $this->assertInstanceOf('Magento\Mview\View\Collection', $collection->loadData());
    }
}
