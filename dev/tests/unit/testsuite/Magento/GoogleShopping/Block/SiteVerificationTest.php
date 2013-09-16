<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleShopping_Block_SiteVerificationTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_GoogleShopping_Block_SiteVerification */
    protected $_object;

    /** @var Magento_GoogleShopping_Model_Config */
    protected $_config;

    protected function setUp()
    {
        $objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $layout = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
        $coreHelper = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $coreHelper->expects($this->any())
            ->method('escapeHtml')->with('Valor & Honor')->will($this->returnValue('Valor &amp; Honor'));
        $helperFactory = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($coreHelper));
        $layout->expects($this->any())
            ->method('helper')->with('Magento_Core_Helper_Data')->will($this->returnValue($coreHelper));
        $context = $objectHelper->getObject('Magento_Core_Block_Context', array(
            'eventManager' => $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            'layout' => $layout,
            'helperFactory' => $helperFactory
        ));
        $this->_config = $this->getMock('Magento_GoogleShopping_Model_Config', array(), array(), '', false);
        $this->_block = new Magento_GoogleShopping_Block_SiteVerification($context, $this->_config);
    }

    public function testToHtmlWithContent()
    {
        $this->_config->expects($this->once())
            ->method('getConfigData')->with('verify_meta_tag')->will($this->returnValue('Valor & Honor'));
        $this->assertEquals(
            '<meta name="google-site-verification" content="Valor &amp; Honor"/>',
            $this->_block->toHtml()
        );
    }

    public function testToHtmlWithoutContent()
    {
        $this->_config->expects($this->once())
            ->method('getConfigData')->with('verify_meta_tag')->will($this->returnValue(''));
        $this->assertEquals('', $this->_block->toHtml());
    }
}
