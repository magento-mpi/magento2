<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data\Tree\Node;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Tree\Node\Collection
     */
    protected $collection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    protected function setUp()
    {
        $this->containerMock = $this->getMock('Magento\Framework\Data\Tree\Node', [], [], '', false);
        $this->collection = new \Magento\Framework\Data\Tree\Node\Collection($this->containerMock);
    }

    /**
     * @param int $number
     * @param \PHPUnit_Framework_MockObject_MockObject|null $returnValue
     *
     * @dataProvider testAddDataProvider
     */
    public function testAdd($number, $returnValue)
    {
        $nodeMock = $this->getMock('Magento\Framework\Data\Tree\Node', [], [], '', false);
        $nodeMock->expects($this->once())->method('setParent')->with($this->containerMock);
        $this->containerMock
            ->expects($this->exactly($number))
            ->method('getTree')
            ->will($this->returnValue($returnValue));
        $nodeMock->expects($this->once())->method('getId')->will($this->returnValue('id_node'));
        $this->assertEquals($nodeMock, $this->collection->add($nodeMock));
        $this->assertEquals(['id_node' => $nodeMock], $this->collection->getNodes());
        $this->assertEquals($nodeMock, $this->collection->searchById('id_node'));
        $this->assertEquals(null, $this->collection->searchById('id_node_new'));
    }

    public function testAddDataProvider()
    {
        return [
            'tree_exists' => [2, $this->getMock('Magento\Framework\Data\Tree', [], [], '', false)],
            'tree_not_exist' => [1, null]
        ];
    }
}
