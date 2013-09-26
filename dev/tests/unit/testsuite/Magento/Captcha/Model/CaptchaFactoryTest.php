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

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_model = new Magento_Captcha_Model_CaptchaFactory($this->_objectManagerMock);
    }

    public function testCreatePositive()
    {
        $captchaType = 'default';

        $defaultCaptchaMock = $this->getMock('Magento_Captcha_Model_Default', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Captcha_Model_' . ucfirst($captchaType)))
            ->will($this->returnValue($defaultCaptchaMock));

        $this->assertEquals($defaultCaptchaMock, $this->_model->create($captchaType, 'form_id'));
    }

    public function testCreateNegative()
    {
        $captchaType = 'wrong_instance';

        $defaultCaptchaMock = $this->getMock('stdClass', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Captcha_Model_' . ucfirst($captchaType)))
            ->will($this->returnValue($defaultCaptchaMock));

        $this->setExpectedException('InvalidArgumentException',
            'Magento_Captcha_Model_' . ucfirst($captchaType) . ' does not implement Magento_Captcha_Model_Interface');

        $this->assertEquals($defaultCaptchaMock, $this->_model->create($captchaType, 'form_id'));
    }
}
