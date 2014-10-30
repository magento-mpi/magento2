<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model;

class IndexerRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCreatesIndexerInstancesAndReusesExistingOnes()
    {
        $firstIndexer = $this->getMock('Magento\Indexer\Model\IndexerInterface');
        $firstIndexer->expects($this->once())->method('load')->with('first-indexer')->willReturnSelf();

        $secondIndexer = $this->getMock('Magento\Indexer\Model\IndexerInterface');
        $secondIndexer->expects($this->once())->method('load')->with('second-indexer')->willReturnSelf();

        $objectManager = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $objectManager->expects($this->at(0))->method('create')->willReturn($firstIndexer);
        $objectManager->expects($this->at(1))->method('create')->willReturn($secondIndexer);

        $unit = new IndexerRegistry($objectManager);
        $this->assertSame($firstIndexer, $unit->get('first-indexer'));
        $this->assertSame($secondIndexer, $unit->get('second-indexer'));
        $this->assertSame($firstIndexer, $unit->get('first-indexer'));
    }
}
