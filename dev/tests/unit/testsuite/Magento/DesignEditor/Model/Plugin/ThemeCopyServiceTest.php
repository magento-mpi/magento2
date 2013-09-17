<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_DesignEditor_Model_Plugin_ThemeCopyServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_DesignEditor_Model_Plugin_ThemeCopyService
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento_DesignEditor_Model_Theme_ChangeFactory',
            array('create'), array(), '', false);
        $this->_model = new Magento_DesignEditor_Model_Plugin_ThemeCopyService($this->_factoryMock);
    }

    public function testAroundCopySavesChangeTimeIfSourceThemeHasBeenAlreadyChanged()
    {
        $sourceThemeId = 1;
        $sourceChangeTime = '21:00:00';
        $targetThemeId = 2;

        $sourceThemeMock = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false);
        $sourceThemeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($sourceThemeId));

        $targetThemeMock = $this->getMock('Magento_Core_Model_Theme', array(), array(), '', false);
        $targetThemeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($targetThemeId));

        $sourceChangeMock = $this->getMock('Magento_DesignEditor_Model_Theme_Change',
            array('getId', 'getChangeTime', 'loadByThemeId'), array(), '', false);
        $targetChangeMock = $this->getMock('Magento_DesignEditor_Model_Theme_Change',
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
        $invocationChainMock = $this->getMock('Magento_Code_Plugin_InvocationChain', array(), array(), '', false);
        $invocationChainMock->expects($this->once())
            ->method('proceed')
            ->with($methodArguments);

        $this->_model->aroundCopy($methodArguments, $invocationChainMock);
    }

}
