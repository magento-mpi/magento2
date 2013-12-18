<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testGetUrl()
    {
        $path = 'checkout';
        $url = 'http://example.com/';

        $urlBuilder = $this->getMockForAbstractClass('Magento\UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url . $path));

        $context = $this->_objectManagerHelper->getObject(
            'Magento\View\Element\Template\Context',
            array('urlBuilder' => $urlBuilder)
        );
        $link = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Link',
            array(
                'context' => $context,
            )
        );
        $this->assertEquals($url . $path, $link->getHref());
    }

    /**
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($canOnepageCheckout, $isOutputEnabled)
    {
        $helper = $this->getMockBuilder('Magento\Checkout\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('canOnepageCheckout', 'isModuleOutputEnabled'))
            ->getMock();

        $moduleManager = $this->getMockBuilder('Magento\Module\Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('isOutputEnabled'))
            ->getMock();

        /** @var \Magento\Checkout\Block\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Link',
            array(
                'moduleManager' => $moduleManager,
                'checkoutHelper' => $helper,
            )
        );
        $helper->expects($this->any())->method('canOnepageCheckout')->will($this->returnValue($canOnepageCheckout));
        $moduleManager->expects($this->any())
            ->method('isOutputEnabled')
            ->with('Magento_Checkout')
            ->will($this->returnValue($isOutputEnabled));
        $this->assertEquals('', $block->toHtml());
    }

    public function toHtmlDataProvider()
    {
        return array(
            array(false, true),
            array(true, false),
            array(false, false)
        );
    }
}
