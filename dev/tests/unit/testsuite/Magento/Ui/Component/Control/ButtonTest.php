<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Control;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Button
     */
    protected $button;

    /**
     * @var Context| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var UrlInterface| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * Escaper
     *
     * @var Escaper| \PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    public function setUp()
    {
        $this->contextMock = $this->getMock(
            '\Magento\Framework\View\Element\Template\Context',
            ['getPageLayout', 'getUrlBuilder', 'getEscaper'],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->escaperMock = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false);
        $this->contextMock->expects($this->any())->method('getEscaper')->willReturn($this->escaperMock);
        $this->button = new Button($this->contextMock);
    }

    public function testGetType()
    {
        $this->assertEquals('button', $this->button->getType());
    }

    public function testGetOnClick()
    {
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('', [])->willReturn('url');
        $this->assertEquals("setLocation('url');", $this->button->getOnClick());
    }

    public function testGetOnClickHasData()
    {
        $this->button->setData('url', 'url2');
        $this->assertEquals("setLocation('url2');", $this->button->getOnClick());
    }

    public function testGetAttributesHtml()
    {
        $expected = 'type="button" class="action- scalable classValue disabled" ' .
            'onclick="setLocation(\'url2\');" disabled="disabled" data-attributeKey="attributeValue" ';
        $this->button->setDisabled(true);
        $this->button->setData('url', 'url2');
        $this->button->setData('class', 'classValue');
        $this->button->setDataAttribute(['attributeKey' => 'attributeValue']);
        $this->escaperMock->expects($this->any())->method('escapeHtml')->withAnyParameters()->willReturnArgument(0);
        $this->assertEquals($expected, $this->button->getAttributesHtml());
    }
}
