<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_model;

    /**
     * @var \Magento\Config\ScopeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeMock;

    protected function setUp()
    {
        $this->_scopeMock = $this->getMockForAbstractClass(
            'Magento\Config\ScopeInterface',
            array('setCurrentScope'),
            '',
            false
        );
        $this->_model = new \Magento\Framework\App\State($this->_scopeMock, time());
    }

    public function testSetAreaCode()
    {
        $areaCode = 'some code';
        $this->_scopeMock->expects($this->once())->method('setCurrentScope')->with($areaCode);
        $this->_model->setAreaCode($areaCode);
        $this->setExpectedException('Magento\Exception');
        $this->_model->setAreaCode('any code');
    }

    public function testGetAreaCodeException()
    {
        $this->_scopeMock->expects($this->never())->method('setCurrentScope');
        $this->setExpectedException('Magento\Exception');
        $this->_model->getAreaCode();
    }

    public function testGetAreaCode()
    {
        $areaCode = 'some code';
        $this->_scopeMock->expects($this->once())->method('setCurrentScope')->with($areaCode);
        $this->_model->setAreaCode($areaCode);
        $this->assertEquals($areaCode, $this->_model->getAreaCode());
    }

    public function testEmulateAreaCode()
    {
        $areaCode = 'original code';
        $emulatedCode = 'emulated code';
        $this->_scopeMock->expects($this->once())->method('setCurrentScope')->with($areaCode);
        $this->_model->setAreaCode($areaCode);
        $this->assertEquals(
            $emulatedCode,
            $this->_model->emulateAreaCode($emulatedCode, array($this, 'emulateAreaCodeCallback'))
        );
        $this->assertEquals($this->_model->getAreaCode(), $areaCode);
    }

    public function emulateAreaCodeCallback()
    {
        return $this->_model->getAreaCode();
    }
}
