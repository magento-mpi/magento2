<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code\Model\File\Validator;

class NotProtectedExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\File\Validator\NotProtectedExtension
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var string
     */
    protected $_protectedList = 'exe,php,jar';

    protected function setUp()
    {
        $this->_storeConfig = $this->getMock('\Magento\App\Config\ScopeConfigInterface');
        $this->_storeConfig->expects($this->atLeastOnce())->method('getValue')->with($this->equalTo(
                \Magento\Core\Model\File\Validator\NotProtectedExtension::XML_PATH_PROTECTED_FILE_EXTENSIONS
            ),
            $this->equalTo(\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            $this->equalTo(null))->will($this->returnValue($this->_protectedList));
        $this->_model = new \Magento\Core\Model\File\Validator\NotProtectedExtension($this->_storeConfig);
    }

    public function testGetProtectedFileExtensions()
    {
        $this->assertEquals($this->_protectedList, $this->_model->getProtectedFileExtensions());
    }

    public function testInitialization()
    {
        $property = new \ReflectionProperty(
            '\Magento\Core\Model\File\Validator\NotProtectedExtension',
            '_messageTemplates'
        );
        $property->setAccessible(true);
        $defaultMess = array(
            'protectedExtension' => __('File with an extension "%value%" is protected and cannot be uploaded')
        );
        $this->assertEquals($defaultMess, $property->getValue($this->_model));

        $property = new \ReflectionProperty(
            '\Magento\Core\Model\File\Validator\NotProtectedExtension',
            '_protectedFileExtensions'
        );
        $property->setAccessible(true);
        $protectedList = array('exe', 'php', 'jar');
        $this->assertEquals($protectedList, $property->getValue($this->_model));
    }

    public function testIsValid()
    {
        $this->assertTrue($this->_model->isValid('html'));
        $this->assertTrue($this->_model->isValid('jpg'));
        $this->assertFalse($this->_model->isValid('php'));
        $this->assertFalse($this->_model->isValid('jar'));
        $this->assertFalse($this->_model->isValid('exe'));
    }
}
