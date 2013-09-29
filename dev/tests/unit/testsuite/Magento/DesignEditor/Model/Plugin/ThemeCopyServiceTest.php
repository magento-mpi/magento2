<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Plugin;

class ThemeCopyServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DesignEditor\Model\Plugin\ThemeCopyService
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento\DesignEditor\Model\Theme\ChangeFactory',
            array('create'), array(), '', false);
        $this->_model = new \Magento\DesignEditor\Model\Plugin\ThemeCopyService($this->_factoryMock);
    }

    public function testAroundCopySavesChangeTimeIfSourceThemeHasBeenAlreadyChanged()
    {
        $sourceThemeId = 1;
        $sourceChangeTime = '21:00:00';
        $targetThemeId = 2;

        $sourceThemeMock = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $sourceThemeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($sourceThemeId));

        $targetThemeMock = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $targetThemeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($targetThemeId));

        $sourceChangeMock = $this->getMock('Magento\DesignEditor\Model\Theme\Change',
            array('getId', 'getChangeTime', 'loadByThemeId'), array(), '', false);
        $targetChangeMock = $this->getMock('Magento\DesignEditor\Model\Theme\Change',
            array('setThemeId', 'setChangeTime', 'loadByThemeId', 'save'), array(), '', false);
        $this->_factoryMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue($sourceChangeMock));
        $this->_factoryMock->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($targetChangeMock));

        $sourceChangeMock->expects($this->once())
            ->method('loadByThemeId')
            ->with($sourceThemeId);
        $sourceChangeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(10));
        $sourceChangeMock->expects($this->any())
            ->method('getChangeTime')
            ->will($this->returnValue($sourceChangeTime));

        $targetChangeMock->expects($this->once())
            ->method('loadByThemeId')
            ->with($targetThemeId);
        $targetChangeMock->expects($this->once())
            ->method('setThemeId')
            ->with($targetThemeId);
        $targetChangeMock->expects($this->once())
            ->method('setChangeTime')
            ->with($sourceChangeTime);
        $targetChangeMock->expects($this->once())
            ->method('save');

        $methodArguments = array($sourceThemeMock, $targetThemeMock);
        $invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with($methodArguments);

        $this->_model->aroundCopy($methodArguments, $invocationChainMock);
    }

}
