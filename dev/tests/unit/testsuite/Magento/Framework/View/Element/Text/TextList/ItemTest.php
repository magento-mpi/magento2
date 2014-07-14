<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for view BlockPool model
 */
namespace Magento\Framework\View\Element\Text\TextList;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Item
     */
    protected $item;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->item = $objectManager->getObject('Magento\Framework\View\Element\Text\TextList\Item');
    }

    public function testSetLink()
    {
        $liParams = ['class' => 'some-css-class'];
        $innerText = 'text';

        $this->assertNull($this->item->getLiParams());
        $this->assertNull($this->item->getInnerText());

        $this->item->setLink($liParams, $innerText);

        $this->assertEquals($liParams, $this->item->getLiParams());
        $this->assertEquals($innerText, $this->item->getInnerText());
    }

    /**
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($liParams, $attrName, $attrValue, $innerText)
    {
        $this->item->setLink($liParams, $innerText);
        $this->assertTag([
            'tag' => 'li',
            'attributes' => [$attrName => $attrValue],
            'content' => $innerText
        ], $this->item->toHtml());
    }

    public function toHtmlDataProvider()
    {
        return [
            [
                'liParams' => ['class' => 'some-css-class'],
                'attrName' => 'class',
                'attrValue' => 'some-css-class',
                'innerText' => 'text',
            ], [
                'liParams' => 'class="some-css-class"',
                'attrName' => 'class',
                'attrValue' => 'some-css-class',
                'innerText' => 'text',
            ]
        ];
    }
}
