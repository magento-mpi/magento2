<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\View\Page\Config
 */
namespace Magento\Framework\View\Page;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Page\Title
     */
    protected $title;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $structure;

    public function setUp()
    {
        $this->structure = $this->getMockBuilder('Magento\Framework\View\Page\Config\Structure')
            ->disableOriginalConstructor()
            ->getMock();

        $this->title = new Title($this->structure);
    }

    public function testSet()
    {
        $value = 'test_value';
        $this->title->set($value);
        $this->assertEquals(['test_value'], $this->title->get());
    }

    public function testUnset()
    {
        $value = ['test'];
        $this->title->set($value);
        $this->assertEquals('test', $this->title->getAsString());
        $this->assertNull($this->title->unsetValue());
    }

    public function testGetCompositeValue()
    {
        $this->structure->expects($this->once())->method('getTitle')->will($this->returnValue('test'));
        $this->assertEquals(['test'], $this->title->get());
    }

    public function testGetAsString()
    {
        $value = 'test';
        $this->title->set($value);
        $this->assertEquals('test', $this->title->getAsString());
    }

    public function testGetAsStringArray()
    {
        $value = ['test', 'test2'];
        $this->title->set($value);
        $this->assertEquals('test / test2', $this->title->getAsString());
    }
}
