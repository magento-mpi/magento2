<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Image
 */
namespace Magento\Framework\Data\Form\Element;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Image
     */
    protected $_image;

    protected function setUp()
    {
        $factoryMock = $this->getMock('\Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactoryMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $escaperMock = $this->getMock('\Magento\Framework\Escaper', array(), array(), '', false);
        $urlBuilderMock = $this->getMock('\Magento\Framework\Url', array(), array(), '', false);
        $this->_image = new \Magento\Framework\Data\Form\Element\Image(
            $factoryMock,
            $collectionFactoryMock,
            $escaperMock,
            $urlBuilderMock
        );
        $formMock = new \Magento\Framework\Object();
        $formMock->getHtmlIdPrefix('id_prefix');
        $formMock->getHtmlIdPrefix('id_suffix');
        $this->_image->setForm($formMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Image::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('file', $this->_image->getType());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Image::getName
     */
    public function testGetName()
    {
        $this->_image->setName('image_name');
        $this->assertEquals('image_name', $this->_image->getName());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Image::getElementHtml
     */
    public function testGetElementHtmlWithoutValue()
    {
        $html = $this->_image->getElementHtml();
        $this->assertContains('class="input-file"', $html);
        $this->assertContains('<input', $html);
        $this->assertContains('type="file"', $html);
        $this->assertContains('value=""', $html);
        $this->assertNotContains('</a>', $html);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Image::getElementHtml
     */
    public function testGetElementHtmlWithValue()
    {
        $this->_image->setValue('test_value');
        $html = $this->_image->getElementHtml();
        $this->assertContains('class="input-file"', $html);
        $this->assertContains('<input', $html);
        $this->assertContains('type="file"', $html);
        $this->assertContains('value="test_value"', $html);
        $this->assertContains('<a href="test_value" onclick="imagePreview(\'_image\'); return false;"', $html);
        $this->assertContains('<input type="checkbox"', $html);
    }
}
