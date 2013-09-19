<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Cart_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testGetUrl()
    {
        $path = 'checkout/cart';
        $url = 'http://example.com/';

        $urlBuilder = $this->getMockForAbstractClass('Magento\Core\Model\UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url . $path));

        $helper = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->_objectManagerHelper->getObject(
            'Magento\Core\Block\Template\Context',
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
        $moduleManager = $this->getMockBuilder('Magento\Core\Model\ModuleManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isOutputEnabled'))
            ->getMock();
        $helper = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Magento\Core\Block\Template\Context',
            array(
                'helperFactory' => $helperFactory
            )
        );

        /** @var \Magento\Invitation\Block\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Link',
            array(
                'context' => $context,
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
        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        /** @var  \Magento\Core\Block\Template\Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Magento\Core\Block\Template\Context',
            array(
                'helperFactory' => $helperFactory
            )
        );

        /** @var \Magento\Invitation\Block\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\Checkout\Block\Cart\Link',
            array(
                'context' => $context,
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
