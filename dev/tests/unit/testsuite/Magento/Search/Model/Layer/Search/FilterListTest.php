<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

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
     * @var \Magento\Search\Model\Layer\Search\FilterList
     */
    protected $model;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('\Magento\Framework\ObjectManager');
        $this->attributeListMock = $this->getMock(
            '\Magento\Search\Model\Layer\Search\FilterableAttributeList',
            array(),
            array(),
            '',
            false
        );
        $this->searchHelperMock = $this->getMock('\Magento\Search\Helper\Data', array(), array(), '', false);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);

        $this->model = new FilterList($this->objectManagerMock, $this->attributeListMock, $this->searchHelperMock);
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\FilterList::getFilters
     * @covers \Magento\Search\Model\Layer\Search\FilterList::__construct
     */
    public function testGetFiltersThirdPartSearchEngineIsTurnedOff()
    {
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('filter'));

        $this->attributeListMock->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array()));

        $this->assertEquals(array('filter'), $this->model->getFilters($this->layerMock));
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\FilterList::getFilters
     */
    public function testGetFiltersThirdPartSearchEngineIsAvailable()
    {
        $filterMock = $this->getMock(
            '\Magento\Search\Model\Layer\Category\Filter\Category',
            array(),
            array(),
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
            ->will($this->returnValue(array()));

        $this->searchHelperMock->expects($this->once())
            ->method('isThirdPartSearchEngine')
            ->will($this->returnValue(true));

        $this->searchHelperMock->expects($this->once())
            ->method('getIsEngineAvailableForNavigation')
            ->with(false)
            ->will($this->returnValue(true));

        $this->assertEquals(array($filterMock), $this->model->getFilters($this->layerMock));
    }
}
