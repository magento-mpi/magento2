<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Model\Resource\Sales;

use Magento\Customer\Model\Attribute;
use Magento\Framework\DB\Ddl\Table;

class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var \Magento\Sales\Model\Resource\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parentResourceModelMock;

    protected function setUp()
    {
        $this->resourceMock = $this->getMock('Magento\Framework\App\Resource', [], [], '', false);
        $this->connectionMock = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->parentResourceModelMock = $this->getMock('Magento\Sales\Model\Resource\Quote', [], [], '', false);

        $this->resourceMock->expects($this->any())
            ->method('getConnection')
            ->with('core_write')
            ->will($this->returnValue($this->connectionMock));
        $this->resourceMock->expects($this->any())
            ->method('getTableName')
            ->will($this->returnArgument(0));

        $this->quote = new \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote(
            $this->resourceMock,
            $this->parentResourceModelMock
        );
    }

    /**
     * @param string $backendType
     * @dataProvider dataProviderSaveNewAttributeNegative
     */
    public function testSaveNewAttributeNegative($backendType)
    {
        $attributeMock = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->once())
            ->method('getBackendType')
            ->will($this->returnValue($backendType));

        $this->connectionMock->expects($this->never())
            ->method('addColumn');

        $this->assertEquals($this->quote, $this->quote->saveNewAttribute($attributeMock));
    }

    /**
     * @return array
     */
    public function dataProviderSaveNewAttributeNegative()
    {
        return [
            [''],
            [Attribute::TYPE_STATIC],
            ['something_wrong'],
        ];
    }

    /**
     * @param string $backendType
     * @param array $definition
     * @dataProvider dataProviderSaveNewAttribute
     */
    public function testSaveNewAttribute($backendType, array $definition)
    {
        $attributeMock = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->once())
            ->method('getBackendType')
            ->will($this->returnValue($backendType));
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code'));

        $definition['comment'] = 'Customer Attribute Code';

        $this->connectionMock->expects($this->once())
            ->method('addColumn')
            ->with('magento_customercustomattributes_sales_flat_quote', 'customer_attribute_code', $definition, null);

        $this->assertEquals($this->quote, $this->quote->saveNewAttribute($attributeMock));
    }

    /**
     * @return array
     */
    public function dataProviderSaveNewAttribute()
    {
        return [
            ['datetime', ['type' => Table::TYPE_DATE]],
            ['decimal', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4']],
            ['int', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER]],
            ['text', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT]],
            ['varchar', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255]],
        ];
    }

    public function testDeleteAttribute()
    {
        $attributeMock = $this->getMock('Magento\Customer\Model\Attribute', [], [], '', false);
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code'));

        $this->connectionMock->expects($this->once())
            ->method('dropColumn')
            ->with('magento_customercustomattributes_sales_flat_quote', 'customer_attribute_code', null);

        $this->assertEquals($this->quote, $this->quote->deleteAttribute($attributeMock));
    }

    public function testIsEntityExistsNoId()
    {
        $salesMock = $this->getMock('Magento\CustomerCustomAttributes\Model\Sales\AbstractSales', [], [], '', false);
        $salesMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(0));

        $this->connectionMock->expects($this->never())
            ->method('select');
        $this->connectionMock->expects($this->never())
            ->method('fetchOne');

        $this->assertEquals(false, $this->quote->isEntityExists($salesMock));
    }

    /**
     * @param string $fetchedColumn
     * @param bool $result
     * @dataProvider dataProviderIsEntityExists
     */
    public function testIsEntityExists($fetchedColumn, $result)
    {
        $salesMock = $this->getMock('Magento\CustomerCustomAttributes\Model\Sales\AbstractSales', [], [], '', false);
        $salesMock->expects($this->exactly(2))
            ->method('getId')
            ->will($this->returnValue(1));

        $selectMock = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);

        $this->connectionMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($selectMock));

        $this->parentResourceModelMock->expects($this->once())
            ->method('getMainTable')
            ->will($this->returnValue('parent_table'));
        $this->parentResourceModelMock->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue('parent_id'));

        $selectMock->expects($this->once())
            ->method('from')
            ->with('parent_table', 'parent_id')
            ->will($this->returnSelf());
        $selectMock->expects($this->once())
            ->method('forUpdate')
            ->with(true)
            ->will($this->returnSelf());
        $selectMock->expects($this->once())
            ->method('where')
            ->with("parent_id = ?", 1)
            ->will($this->returnSelf());

        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->with($selectMock)
            ->will($this->returnValue($fetchedColumn));

        $this->assertEquals($result, $this->quote->isEntityExists($salesMock));
    }

    /**
     * @return array
     */
    public function dataProviderIsEntityExists()
    {
        return [
            ['', false],
            ['some_value', true],
        ];
    }
}
