<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart;

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
        $path = 'checkout/cart';
        $url = 'http://example.com/';

        $urlBuilder = $this->getMockForAbstractClass('Magento\UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url . $path));

        $helper = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->_objectManagerHelper->getObject(
            'Magento\View\Element\Template\Context',
            array('urlBuilder' => $urlBuilder)
        );
        $link = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Link',
            array(
                'coreData' => $helper,
                'context' => $context
            )
        );
        $this->assertSame($url . $path, $link->getHref());
    }

    public function testToHtml()
    {
        $moduleManager = $this->getMockBuilder('Magento\Module\Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('isOutputEnabled'))
            ->getMock();
        $helper = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Checkout\Block\Cart\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Link',
            array(
                'cartHelper' => $helper,
                'moduleManager' => $moduleManager
            )
        );
        $moduleManager->expects($this->any())
            ->method('isOutputEnabled')
            ->with('Magento_Checkout')
            ->will($this->returnValue(true));
        $this->assertSame('', $block->toHtml());
    }

    /**
     * @dataProvider getLabelDataProvider
     */
    public function testGetLabel($productCount, $label)
    {
        $helper = $this->getMockBuilder('Magento\Checkout\Helper\Cart')
            ->disableOriginalConstructor()
            ->setMethods(array('getSummaryCount'))
            ->getMock();

        /** @var \Magento\Checkout\Block\Cart\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Link',
            array(
                'cartHelper' => $helper,
            )
        );
        $helper->expects($this->any())->method('getSummaryCount')->will($this->returnValue($productCount));
        $this->assertSame($label, (string)$block->getLabel());
    }

    public function getLabelDataProvider()
    {
        return array(
            array(1, 'My Cart (1 item)'),
            array(2, 'My Cart (2 items)'),
            array(0, 'My Cart')
        );
    }
}
