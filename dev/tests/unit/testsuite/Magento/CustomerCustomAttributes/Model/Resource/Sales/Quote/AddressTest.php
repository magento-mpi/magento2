<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote;


class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address
     */
    protected $address;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Sales\Model\Resource\Quote\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parentResourceModelMock;

    protected function setUp()
    {
        $this->resourceMock = $this->getMock('Magento\Framework\App\Resource', [], [], '', false);
        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->parentResourceModelMock = $this->getMock(
            'Magento\Sales\Model\Resource\Quote\Address', [], [], '', false
        );

        $this->resourceMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->connectionMock));
        $this->resourceMock->expects($this->any())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $this->address = new \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote\Address(
            $this->resourceMock,
            $this->parentResourceModelMock
        );
    }

    public function testAttachDataToEntitiesNoItems()
    {
        $this->connectionMock->expects($this->never())
            ->method('select');
        $this->connectionMock->expects($this->never())
            ->method('fetchAll');

        $this->assertEquals($this->address, $this->address->attachDataToEntities([]));
    }

    public function testAttachDataToEntities()
    {
        $items = [];
        $itemIds = [];
        $rowSet = [];
        for ($i = 1; $i <= 3; $i++) {
            $row = ['entity_id' => $i, 'value' => $i];

            $item = $this->getMock('Magento\Framework\Object', [], [], '', false);
            $item->expects($this->exactly(2))
                ->method('getId')
                ->will($this->returnValue($i));
            $item->expects($this->once())
                ->method('addData')
                ->with($row);

            $items[] = $item;
            $itemIds[] = $i;
            $rowSet[] = $row;
        }

        $selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);

        $this->connectionMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($selectMock));

        $selectMock->expects($this->once())
            ->method('from')
            ->with('magento_customercustomattributes_sales_flat_quote_address')
            ->will($this->returnSelf());
        $selectMock->expects($this->once())
            ->method('where')
            ->with("entity_id IN (?)", $itemIds)
            ->will($this->returnSelf());

        $this->connectionMock->expects($this->once())
            ->method('fetchAll')
            ->with($selectMock)
            ->will($this->returnValue($rowSet));

        $this->assertEquals($this->address, $this->address->attachDataToEntities($items));
    }
}
