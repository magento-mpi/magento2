<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Page\Config;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Layout\Element as LayoutElement;
use Magento\Framework\View\Page\Config as PageConfig;

/**
 * Test for page config reader model
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureMock;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->structureMock = $this->getMockBuilder('Magento\Framework\View\Page\Config\Structure')
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->reader = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Page\Config\Reader',
            [
                'structure' => $this->structureMock
            ]
        );
    }

    public function testReadHead()
    {
        $this->structureMock->expects($this->once())
            ->method('setTitle')
            ->with('Test title');

        $this->structureMock->expects($this->once())
            ->method('setMetaData')
            ->with('meta_name', 'meta_content');

        $this->structureMock->expects($this->exactly(3))
            ->method('addAssets')
            ->withConsecutive(
                array('path/file.css', ['src' => 'path/file.css', "media" => "all"]),
                array(
                    'mage/jquery-no-conflict.js',
                    ['src' => 'mage/jquery-no-conflict.js', "ie_condition" => "lt IE 7"]
                ),
                array('path/file.js', ['src' => 'path/file.js', "defer" => "defer"])
            );

        $this->structureMock->expects($this->once())
            ->method('removeAssets')
            ->with('path/remove/file.css');

        $this->structureMock->expects($this->once())
            ->method('setElementAttribute')
            ->with(PageConfig::ELEMENT_TYPE_HEAD, 'head_attribute_name', 'head_attribute_value');

        $xmlElement = new LayoutElement(file_get_contents(__DIR__ . '/_files/template_head.xml'));
        $this->assertEquals($this->reader, $this->reader->readHead(current($xmlElement->children())));
    }

    public function testReadHtml()
    {
        $this->structureMock->expects($this->once())
            ->method('setElementAttribute')
            ->with(PageConfig::ELEMENT_TYPE_HTML, 'html_attribute_name', 'html_attribute_value');

        $xmlElement = new LayoutElement(file_get_contents(__DIR__ . '/_files/template_html.xml'));
        $this->assertEquals($this->reader, $this->reader->readHtml(current($xmlElement->children())));
    }

    public function testReadBody()
    {
        $this->structureMock->expects($this->once())
            ->method('setElementAttribute')
            ->with(PageConfig::ELEMENT_TYPE_BODY, 'body_attribute_name', 'body_attribute_value');

        $xmlElement = new LayoutElement(file_get_contents(__DIR__ . '/_files/template_body.xml'));
        $this->assertEquals($this->reader, $this->reader->readBody(current($xmlElement->children())));
    }
}
