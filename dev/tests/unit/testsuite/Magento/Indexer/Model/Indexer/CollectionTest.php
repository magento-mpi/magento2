<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model\Indexer;

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

        $config = $this->getMockBuilder('Magento\Indexer\Model\ConfigInterface')
            ->getMock();

        $statesFactory = $this->getMockBuilder('Magento\Indexer\Model\Resource\Indexer\State\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $states = $this->getMockBuilder('Magento\Indexer\Model\Resource\Indexer\State\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $state = $this->getMockBuilder('Magento\Indexer\Model\Indexer\State')
            ->setMethods(array('getIndexerId', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $state->expects($this->any())
            ->method('getIndexerId')
            ->will($this->returnValue('second_indexer_id'));

        $indexer = $this->getMockBuilder('Magento\Indexer\Model\Indexer\Collection')
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
            ->with('Magento\Indexer\Model\Indexer')
            ->will($this->returnValue($indexer));

        $statesFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($states));

        $config->expects($this->once())
            ->method('getIndexerIds')
            ->will($this->returnValue(array($indexerIdOne, $indexerIdSecond)));

        $states->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($state)));

        $collection = new \Magento\Indexer\Model\Indexer\Collection($entityFactory, $config, $statesFactory);
        $this->assertInstanceOf('Magento\Indexer\Model\Indexer\Collection', $collection->loadData());
    }
}
