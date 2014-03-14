<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search\AvailabilityFlag;

class PluginTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Search\Model\Layer\Search\AvailabilityFlag\Plugin
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Layer\AvailabilityFlagInterface');
        $this->helperMock = $this->getMock('\Magento\Search\Helper\Data', array(), array(), '', false);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->model = new Plugin($this->helperMock);
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag\Plugin::__construct
     */
    public function testIsEnabledWithThirdPartEngineOn()
    {
        $this->helperMock->expects($this->once())->method('isThirdPartSearchEngine')->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isActiveEngine')->will($this->returnValue(true));
        $this->subjectMock->expects($this->once())->method("isEnabled")->will($this->returnValue(false));

        $proceed = function() {
            $this->fail('Subject should not be called in this scenario');
        };
        $this->assertFalse($this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, array()));
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag\Plugin::__construct
     */
    public function testIsEnabledWithThirdPartEngineOff()
    {
        $proceed = function() {
            return false;
        };
        $this->assertFalse($this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, array()));
    }
}
