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

    /** @var \Magento\Captcha\Model\CaptchaFactory */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->_model = new \Magento\Captcha\Model\CaptchaFactory($this->_objectManagerMock);
    }

    public function testCreatePositive()
    {
        $instance = 'sample_captcha_instance';
        $defaultCaptchaMock = $this->getMock('Magento\Captcha\Model\DefaultModel', array(), array(), '', false);
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
            'wrong_instance does not implements \Magento\Captcha\Model\ModelInterface');

        $this->assertEquals($defaultCaptchaMock, $this->_model->create($instance));
    }
}
