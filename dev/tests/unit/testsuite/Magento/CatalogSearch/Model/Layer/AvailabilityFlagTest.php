<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer;

class AvailabilityFlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionMock;

    /**
     * @var \Magento\CatalogSearch\Model\Layer\AvailabilityFlag
     */
    protected $model;

    protected function setUp()
    {
        $this->filterMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Filter\AbstractFilter', array(), array(), '', false
        );
        $this->filters = array($this->filterMock);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->stateMock = $this->getMock('\Magento\Catalog\Model\Layer\State', array(), array(), '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->engineMock = $this->getMock('\Magento\CatalogSearch\Model\Resource\EngineInterface');
        $this->storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false
        );
        $this->engineProviderMock = $this->getMock(
            '\Magento\CatalogSearch\Model\Resource\EngineProvider', array(), array(), '', false
        );

        $this->engineProviderMock->expects($this->any())->method('get')->will($this->returnValue($this->engineMock));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->layerMock->expects($this->any())->method('getProductCollection')
            ->will($this->returnValue($this->collectionMock));

        $this->model = new AvailabilityFlag($this->storeManagerMock, $this->engineProviderMock);
    }

    /**
     * @covers \Magento\CatalogSearch\Model\Layer\Category\AvailabilityFlag::isEnabled
     * @covers \Magento\CatalogSearch\Model\Layer\Category\AvailabilityFlag::__construct
     */
    public function testIsEnabledLayeredNavigationIsNotAllowed()
    {
        $this->engineMock->expects($this->once())
            ->method('isLayeredNavigationAllowed')
            ->will($this->returnValue(false));

        $this->storeMock->expects($this->never())
            ->method('getConfig');

        $this->assertEquals(false, $this->model->isEnabled($this->layerMock, $this->filters));
    }

    /**
     * @param int $collectionSize
     * @param int $availableResCount
     *
     * @dataProvider isEnabledDataProvider
     * @covers \Magento\CatalogSearch\Model\Layer\Category\AvailabilityFlag::isEnabled
     */
    public function testIsEnabledLayeredNavigationIsAllowedParentLogic($collectionSize, $availableResCount)
    {
        $this->engineMock->expects($this->once())
            ->method('isLayeredNavigationAllowed')
            ->will($this->returnValue(true));

        $this->storeMock->expects($this->once())
            ->method('getConfig')
            ->with(AvailabilityFlag::XML_PATH_DISPLAY_LAYER_COUNT)
            ->will($this->returnValue($availableResCount));

        $this->collectionMock->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($collectionSize));

        $this->layerMock->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($this->stateMock));

        $this->stateMock->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array('filter')));

        $this->filterMock->expects($this->any())
            ->method('getItemsCount')
            ->will($this->returnValue(1));

        $this->assertEquals(true, $this->model->isEnabled($this->layerMock, $this->filters));
    }

    /**
     * @return array
     */
    public function isEnabledDataProvider()
    {
        return array(
            array(
                'collectionSize' => 10,
                'availableResCount' => 15
            ),
            array(
                'collectionSize' => 0,
                'availableResCount' => 10
            ),
        );
    }

    /**
     * @covers \Magento\CatalogSearch\Model\Layer\Category\AvailabilityFlag::isEnabled
     */
    public function testIsEnabledLayeredNavigationIsAllowed()
    {
        $this->engineMock->expects($this->once())
            ->method('isLayeredNavigationAllowed')
            ->will($this->returnValue(true));

        $this->storeMock->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue(10));

        $this->collectionMock->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(15));

        $this->layerMock->expects($this->never())
            ->method('getState');

        $this->assertEquals(false, $this->model->isEnabled($this->layerMock, $this->filters));
    }
}
