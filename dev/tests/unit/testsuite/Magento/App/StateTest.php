<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\State
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\App\State(now());
    }

    public function testSetAreaCode()
    {
        $this->_model->setAreaCode('some code');
        $this->setExpectedException('Magento\Exception');
        $this->_model->setAreaCode('any code');
    }

    public function testGetAreaCodeException()
    {
        $this->setExpectedException('Magento\Exception');
        $this->_model->getAreaCode();
    }

    public function testGetAreaCode()
    {
        $areaCode = 'some code';
        $this->_model->setAreaCode($areaCode);
        $this->assertEquals($areaCode, $this->_model->getAreaCode());
    }

    public function testEmulateAreaCode()
    {
        $areaCode = 'original code';
        $emulatedCode = 'emulated code';
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
