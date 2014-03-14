<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

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
     * @var \Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag\Plugin
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Layer\AvailabilityFlagInterface');
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
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

        $this->model = new Plugin($this->storeManagerMock, $this->engineProviderMock);
    }

    /**
     * @covers \Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     * @covers \Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag\Plugin::__construct
     */
    public function testaroundIsEnabledLayeredNavigationIsNotAllowed()
    {
        $this->engineMock->expects($this->once())
            ->method('isLayeredNavigationAllowed')
            ->will($this->returnValue(false));

        $this->storeMock->expects($this->never())
            ->method('getConfig');

        $proceed = function() {
            $this->fail('Proceed should not be called in this scenario');
        };

        $this->assertEquals(
            false, $this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, array())
        );
    }

    /**
     * @param int $collectionSize
     * @param int $availableResCount
     *
     * @dataProvider aroundIsEnabledDataProvider
     * @covers \Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     */
    public function testaroundIsEnabledLayeredNavigationIsAllowedParentLogic($collectionSize, $availableResCount)
    {
        $this->engineMock->expects($this->once())
            ->method('isLayeredNavigationAllowed')
            ->will($this->returnValue(true));

        $this->storeMock->expects($this->once())
            ->method('getConfig')
            ->with(Plugin::XML_PATH_DISPLAY_LAYER_COUNT)
            ->will($this->returnValue($availableResCount));

        $this->collectionMock->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($collectionSize));

        $proceed = function() {
            return true;
        };

        $this->assertEquals(
            true, $this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, array())
        );
    }

    /**
     * @return array
     */
    public function aroundIsEnabledDataProvider()
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
     * @covers \Magento\CatalogSearch\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     */
    public function testaroundIsEnabledLayeredNavigationIsAllowed()
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

        $proceed = function() {
            $this->fail('Proceed should not be called in this scenario');
        };

        $this->assertEquals(
            false, $this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, array())
        );
    }
}
