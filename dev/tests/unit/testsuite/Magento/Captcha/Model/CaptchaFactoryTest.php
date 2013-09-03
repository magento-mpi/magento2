<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Captcha_Model_CaptchaFactoryTest extends PHPUnit_Framework_TestCase
{
    /**@var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var Magento_Captcha_Model_CaptchaFactory */
    protected $_model;

    public function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_model = new Magento_Captcha_Model_CaptchaFactory($this->_objectManagerMock);
    }

    public function testCreatePositive()
    {
        $instance = 'sample_captcha_instance';
        $defaultCaptchaMock = $this->getMock('Magento_Captcha_Model_Default', array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($instance, array())
            ->will($this->returnValue($defaultCaptchaMock));

        $this->assertEquals($defaultCaptchaMock, $this->_model->create($instance));
    }

    public function testCreateNegative()
    {
        $instance = 'wrong_instance';
        $defaultCaptchaMock = $this->getMock('stdClass', array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')
            ->with($instance, array())->will($this->returnValue($defaultCaptchaMock));
        $this->setExpectedException('InvalidArgumentException',
            'wrong_instance does not implements Magento_Captcha_Model_Interface');

        $this->assertEquals($defaultCaptchaMock, $this->_model->create($instance));
    }
}