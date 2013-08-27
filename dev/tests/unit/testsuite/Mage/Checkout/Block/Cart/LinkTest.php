<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Cart_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
    }

    public function testGetUrl()
    {
        $path = 'checkout/cart';
        $url = 'http://example.com/';

        $urlBuilder = $this->getMockForAbstractClass('Mage_Core_Model_UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url . $path));

        $context = $this->_objectManagerHelper->getObject(
            'Mage_Core_Block_Template_Context',
            array('urlBuilder' => $urlBuilder)
        );
        $link = $this->_objectManagerHelper->getObject(
            'Mage_Checkout_Block_Cart_Link',
            array(
                'context' => $context,
            )
        );
        $this->assertSame($url . $path, $link->getHref());
    }

    public function testToHtml()
    {
        $moduleManager = $this->getMockBuilder('Mage_Core_Model_ModuleManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isOutputEnabled'))
            ->getMock();

        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Mage_Core_Block_Template_Context'
        );

        /** @var Enterprise_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Mage_Checkout_Block_Cart_Link',
            array(
                'context' => $context,
                'moduleManager' => $moduleManager
            )
        );
        $moduleManager->expects($this->any())
            ->method('isOutputEnabled')
            ->with('Mage_Checkout')
            ->will($this->returnValue(true));
        $this->assertSame('', $block->toHtml());
    }

    /**
     * @dataProvider getLabelDataProvider
     */
    public function testGetLabel($productCount, $label)
    {
        $helper = $this->getMockBuilder('Mage_Checkout_Helper_Cart')
            ->disableOriginalConstructor()
            ->setMethods(array('getSummaryCount'))
            ->getMock();
        $layout = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();
        $layout->expects($this->once())->method('helper')->will($this->returnValue($helper));

        /** @var  Mage_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Mage_Core_Block_Template_Context',
            array('layout' => $layout)
        );

        /** @var Enterprise_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Mage_Checkout_Block_Cart_Link',
            array(
                'context' => $context,
            )
        );
        $helper->expects($this->any())->method('getSummaryCount')->will($this->returnValue($productCount));
        $this->assertSame($label, $block->getLabel());
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