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

namespace Magento\Core\Block;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Block\Template
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $params = array('layout' => $objectManager->create('Magento\Core\Model\Layout', array()));
        $context = $objectManager->create('Magento\Core\Block\Template\Context', $params);
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Core\Block\Template', '', array('context' => $context));
    }

    public function testConstruct()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Core\Block\Template', '', array('data' => array('template' => 'value')));
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
        $this->_block->setLayout(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout', array('area' => 'some_area')));
        $this->assertEquals('some_area', $this->_block->getArea());
        $this->_block->setArea('another_area');
        $this->assertEquals('another_area', $this->_block->getArea());
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
