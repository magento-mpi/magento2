<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Helper\AbstractHelper|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper = null;

    protected function setUp()
    {
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Helper\Context');
        $this->_helper = $this->getMock('Magento\App\Helper\AbstractHelper', array('_getModuleName'), array($context));
        $this->_helper->expects($this->any())->method('_getModuleName')->will($this->returnValue('Magento_Core'));
    }

    /**
     * @covers \Magento\App\Helper\AbstractHelper::isModuleEnabled
     * @covers \Magento\App\Helper\AbstractHelper::isModuleOutputEnabled
     */
    public function testIsModuleEnabled()
    {
        $this->assertTrue($this->_helper->isModuleEnabled());
        $this->assertTrue($this->_helper->isModuleOutputEnabled());
    }

    public function testUrlEncodeDecode()
    {
        $data = uniqid();
        $result = $this->_helper->urlEncode($data);
        $this->assertNotContains('&', $result);
        $this->assertNotContains('%', $result);
        $this->assertNotContains('+', $result);
        $this->assertNotContains('=', $result);
        $this->assertEquals($data, $this->_helper->urlDecode($result));
    }

    public function testTranslateArray()
    {
        $data = array(uniqid(), array(uniqid(), array(uniqid())));
        $this->assertEquals($data, $this->_helper->translateArray($data));
    }
}
