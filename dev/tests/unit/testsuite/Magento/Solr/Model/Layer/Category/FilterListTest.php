<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Solr\Model\Layer\Category;

class FilterListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeListMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    /**
     * @var \Magento\Solr\Model\Layer\Category\FilterList
     */
    protected $model;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->objectManagerMock = $this->getMock('\Magento\Framework\ObjectManagerInterface');
        $this->attributeListMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Category\FilterableAttributeList',
            [],
            [],
            '',
            false
        );
        $this->searchHelperMock = $this->getMock('\Magento\Solr\Helper\Data', [], [], '', false);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', [], [], '', false);

        $this->model = new FilterList($this->objectManagerMock, $this->attributeListMock, $this->searchHelperMock);
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\FilterList::getFilters
     * @covers \Magento\Solr\Model\Layer\Category\FilterList::__construct
     */
    public function testGetFiltersThirdPartSearchEngineIsTurnedOff()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('filter'));

        $this->attributeListMock->expects($this->once())
            ->method('getList')
            ->will($this->returnValue([]));

        $this->assertEquals(['filter'], $this->model->getFilters($this->layerMock));
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Category\FilterList::getFilters
     */
    public function testGetFiltersThirdPartSearchEngineIsAvailable()
    {
        $filterMock = $this->getMock(
            '\Magento\Solr\Model\Layer\Category\Filter\Category',
            [],
            [],
            '',
            false
        );

        $filterMock->expects($this->once())
            ->method('addFacetCondition');

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($filterMock));

        $this->attributeListMock->expects($this->once())
            ->method('getList')
            ->will($this->returnValue([]));

        $this->searchHelperMock->expects($this->once())
            ->method('isThirdPartSearchEngine')
            ->will($this->returnValue(true));

        $this->searchHelperMock->expects($this->once())
            ->method('getIsEngineAvailableForNavigation')
            ->will($this->returnValue(true));

        $this->assertEquals([$filterMock], $this->model->getFilters($this->layerMock));
    }
}
