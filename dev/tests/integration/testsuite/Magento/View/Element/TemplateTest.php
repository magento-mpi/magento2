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
namespace Magento\View\Element;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Element\Template
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $params = array('layout' => $objectManager->create('Magento\View\Layout', array()));
        $context = $objectManager->create('Magento\View\Element\Template\Context', $params);
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\View\Element\Template',
            '',
            array('context' => $context, 'data' => array('module_name' => 'Magento_View'))
        );
    }

    public function testConstruct()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\View\Element\Template',
            '',
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
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        $this->assertEquals('frontend', $this->_block->getArea());
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\State'
        )->setAreaCode(
            'some_area'
        );
        $this->assertEquals('some_area', $this->_block->getArea());
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\App\State'
        )->setAreaCode(
            'another_area'
        );
        $this->assertEquals('another_area', $this->_block->getArea());
    }

    /**
     * @covers \Magento\View\Element\AbstractBlock::toHtml
     * @see testAssign()
     */
    public function testToHtml()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('any area');
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
