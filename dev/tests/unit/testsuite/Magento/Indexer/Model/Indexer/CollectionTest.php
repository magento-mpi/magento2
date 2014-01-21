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
//if (!$this->isLoaded()) {
//$states = $this->statesFactory->create();
//foreach (array_keys($this->config->getAll()) as $indexerId) {
//    /** @var \Magento\Indexer\Model\Indexer $indexer */
//$indexer = $this->getNewEmptyItem();
//$indexer->load($indexerId);
//foreach ($states->getItems() as $state) {
//    /** @var \Magento\Indexer\Model\Indexer\State $state */
//if ($state->getIndexerId() == $indexerId) {
//$indexer->setState($state);
//break;
//}
//}
//$this->_addItem($indexer);
//}
//$this->_setIsLoaded(true);
//}

    public function testLoadData()
    {
        $indexerId1 = 'first_indexer_id';
        $indexerId2 = 'second_indexer_id';

        $entityFactory = $this->getMockBuilder('Magento\Data\Collection\EntityFactoryInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $config = $this->getMockBuilder('Magento\Indexer\Model\ConfigInterface')
            ->getMock();

        $statesFactory = $this->getMockBuilder('Magento\Indexer\Model\Resource\Indexer\State\CollectionFactory')
            ->disableOriginalConstructor()
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
            ->with($this->logicalOr($indexerId1, $indexerId2));

        $entityFactory->expects($this->any())
            ->method('create')
            ->with('Magento\Indexer\Model\Indexer')
            ->will($this->returnValue($indexer));

        $statesFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($states));

        $config->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue(array($indexerId1 => 1, $indexerId2 => 2)));

        $states->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($state)));

        $collection = new \Magento\Indexer\Model\Indexer\Collection($entityFactory, $config, $statesFactory);
        $this->assertInstanceOf('Magento\Indexer\Model\Indexer\Collection', $collection->loadData());
    }
}
