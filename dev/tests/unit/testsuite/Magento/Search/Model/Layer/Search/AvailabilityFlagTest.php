<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

class AvailabilityFlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var \Magento\Search\Model\Layer\Search\AvailabilityFlag
     */
    protected $model;

    protected function setUp()
    {
        $this->engineProviderMock = $this->getMock(
            '\Magento\CatalogSearch\Model\Resource\EngineProvider', array(), array(), '', false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->helperMock = $this->getMock('\Magento\Search\Helper\Data', array(), array(), '', false);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->stateMock = $this->getMock('\Magento\Catalog\Model\Layer\State', array(), array(), '', false);

        $this->model = new AvailabilityFlag($this->storeManagerMock, $this->engineProviderMock, $this->helperMock);
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag::isEnabled
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag::__construct
     */
    public function testIsEnabledWithThirdPartEngineOn()
    {
        $this->helperMock->expects($this->once())->method('isThirdPartSearchEngine')->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isActiveEngine')->will($this->returnValue(true));
        $this->layerMock->expects($this->any())->method('getState')->will($this->returnValue($this->stateMock));
        $this->stateMock->expects($this->any())->method('getFilters')->will($this->returnValue(array()));

        $this->assertEquals(false, $this->model->isEnabled($this->layerMock, array()));
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag::isEnabled
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag::__construct
     */
    public function testIsEnabledWithThirdPartEngineOff()
    {
        $engineMock = $this->getMock('\Magento\CatalogSearch\Model\Resource\EngineInterface');
        $engineMock->expects($this->once())->method('isLayeredNavigationAllowed')->will($this->returnValue(false));
        $this->engineProviderMock->expects($this->once())->method('get')->will($this->returnValue($engineMock));

        $this->assertEquals(false, $this->model->isEnabled($this->layerMock, array()));
    }
}
