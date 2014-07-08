<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Model\Resource;

class AttributeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\GoogleShopping\Model\Resource\Attribute | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    public function setUp()
    {
        $this->resource = $this->getMockBuilder('\Magento\GoogleShopping\Model\Resource\Attribute')
            ->disableOriginalConstructor()
            ->setMethods(['getReadConnection', 'getTable', ' __wakeup'])
            ->getMock();
    }

    public function testGetRegionsByRegionId()
    {
        $regionId = 1;
        $postalCode = '*';
        $expectedResults = ['A', 'B', 'C'];

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject $mockAdapter */
        $mockAdapter = $this->getMockForAbstractClass('\Magento\Framework\DB\Adapter\AdapterInterface');
        $this->resource->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockAdapter));

        $mockSelect = $this->getMockBuilder('\Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();
        $mockAdapter->expects($this->once())
            ->method('select')
            ->will($this->returnValue($mockSelect));

        $someTableName = 'some_table_name';
        $this->resource->expects($this->once())
            ->method('getTable')
            ->with('directory_country_region')
            ->will($this->returnValue($someTableName));

        $mockSelect->expects($this->once())
            ->method('from')
            ->with(
                ['main_table' => $someTableName],
                ['state' => 'main_table.code']
            )
            ->will($this->returnSelf());

        $mockSelect->expects($this->once())
            ->method('where')
            ->with("main_table.region_id = $regionId");

        $dbResult = [['state' => $expectedResults]];
        $mockAdapter->expects($this->once())
            ->method('fetchAll')
            ->with($mockSelect)
            ->will($this->returnValue($dbResult));

        $this->assertSame([$expectedResults], $this->resource->getRegionsByRegionId($regionId, $postalCode));
    }
}
