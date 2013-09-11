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
     * @var \Magento\Core\Block\Template
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $params = array('layout' => $objectManager->create('Magento\Core\Model\Layout', array()));
        $context = $objectManager->create('Magento\Core\Block\Template\Context', $params);
        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\Core\Block\Template', '',
            array('context' => $context)
        );
    }

    public function testConstruct()
    {
        $block = Mage::app()->getLayout()->createBlock('\Magento\Core\Block\Template', '',
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
        $this->_block->setLayout(Mage::getModel('\Magento\Core\Model\Layout', array('area' => 'some_area')));
        $this->assertEquals('some_area', $this->_block->getArea());
        $this->_block->setArea('another_area');
        $this->assertEquals('another_area', $this->_block->getArea());
    }

    public function testGetDirectOutput()
    {
        $this->assertFalse($this->_block->getDirectOutput());

        $layout = Mage::getModel('\Magento\Core\Model\Layout');
        $layout->setDirectOutput(true);
        $this->_block->setLayout($layout);
        $this->assertTrue($this->_block->getDirectOutput());
    }

    public function testGetShowTemplateHints()
    {
        $this->assertFalse($this->_block->getShowTemplateHints());
    }

    /**
     * @covers \Magento\Core\Block\Template::_toHtml
     * @covers \Magento\Core\Block\AbstractBlock::toHtml
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
        $object = new \Magento\Object(array('key' => 'value'));
        $this->assertEquals('value', $this->_block->getObjectData($object, 'key'));
    }

    public function testGetCacheKeyInfo()
    {
        $this->assertArrayHasKey('template', $this->_block->getCacheKeyInfo());
    }
}
