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

class Magento_Core_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Block_Template
     */
    protected $_block;

    protected function setUp()
    {
        $params = array(
            'layout' => Mage::getObjectManager()->create('Magento_Core_Model_Layout', array())
        );
        $context = Mage::getObjectManager()->create('Magento_Core_Block_Template_Context', $params);
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Core_Block_Template', '',
            array('context' => $context)
        );
    }

    public function testConstruct()
    {
        $block = Mage::app()->getLayout()->createBlock('Magento_Core_Block_Template', '',
            array('data' => array('template' => 'value'))
        );
        $this->assertEquals('value', $block->getTemplate());
    }

    public function testSetGetTemplate()
    {
        $this->assertEmpty($this->_block->getTemplate());
        $this->_block->setTemplate('value');
        $this->assertEquals('value', $this->_block->getTemplate());
    }

    public function testGetArea()
    {
        $this->assertEquals('frontend', $this->_block->getArea());
        $this->_block->setLayout(Mage::getModel('Magento_Core_Model_Layout', array('area' => 'some_area')));
        $this->assertEquals('some_area', $this->_block->getArea());
        $this->_block->setArea('another_area');
        $this->assertEquals('another_area', $this->_block->getArea());
    }

    public function testGetDirectOutput()
    {
        $this->assertFalse($this->_block->getDirectOutput());

        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $layout->setDirectOutput(true);
        $this->_block->setLayout($layout);
        $this->assertTrue($this->_block->getDirectOutput());
    }

    public function testGetShowTemplateHints()
    {
        $this->assertFalse($this->_block->getShowTemplateHints());
    }

    /**
     * @covers Magento_Core_Block_Template::_toHtml
     * @covers Magento_Core_Block_Abstract::toHtml
     * @see testAssign()
     */
    public function testToHtml()
    {
        $this->assertEmpty($this->_block->toHtml());
        $this->_block->setTemplate(uniqid('invalid_filename.phtml'));
        $this->assertEmpty($this->_block->toHtml());
    }

    public function testGetBaseUrl()
    {
        $this->assertEquals('http://localhost/index.php/', $this->_block->getBaseUrl());
    }

    public function testGetObjectData()
    {
        $object = new Magento_Object(array('key' => 'value'));
        $this->assertEquals('value', $this->_block->getObjectData($object, 'key'));
    }

    public function testGetCacheKeyInfo()
    {
        $this->assertArrayHasKey('template', $this->_block->getCacheKeyInfo());
    }
}
