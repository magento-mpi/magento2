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

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Link
     */
    protected $link;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->link = $objectManager->getObject('Magento\Framework\View\Element\Text\TextList\Link');
    }

    public function testSetLink()
    {
        $liParams = ['class' => 'some-css-class'];
        $aParams = ['href' => 'url'];
        $innerText = 'text';
        $afterText = 'afterText';

        $this->assertNull($this->link->getLiParams());
        $this->assertNull($this->link->getAParams());
        $this->assertNull($this->link->getInnerText());
        $this->assertNull($this->link->getAfterText());

        $this->link->setLink($liParams, $aParams, $innerText, $afterText);

        $this->assertEquals($liParams, $this->link->getLiParams());
        $this->assertEquals($aParams, $this->link->getAParams());
        $this->assertEquals($innerText, $this->link->getInnerText());
        $this->assertEquals($afterText, $this->link->getAfterText());
    }

    /**
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($liParams, $aParams, $innerText, $afterText, $result)
    {
        $this->link->setLink($liParams, $aParams, $innerText, $afterText);
        $this->assertStringStartsWith($result, $this->link->toHtml());
    }

    public function toHtmlDataProvider()
    {
        return [
            [
                'liParams' => ['class' => 'some-css-class'],
                'aParams' => ['href' => 'url'],
                'innerText' => 'text',
                'afterText' => 'afterText',
                'result' => '<li class="some-css-class"><a href="url">text</a>afterText</li>'
            ],
            [
                'liParams' => 'class="some-css-class"',
                'aParams' => 'href="url"',
                'innerText' => 'text',
                'afterText' => 'afterText',
                'result' => '<li class="some-css-class"><a href="url">text</a>afterText</li>'
            ]
        ];
    }
}
