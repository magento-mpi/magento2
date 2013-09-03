<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_LinkTest extends PHPUnit_Framework_TestCase
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
        $path = 'checkout';
        $url = 'http://example.com/';

        $urlBuilder = $this->getMockForAbstractClass('Magento_Core_Model_UrlInterface');
        $urlBuilder->expects($this->once())->method('getUrl')->with($path)->will($this->returnValue($url . $path));

        $context = $this->_objectManagerHelper->getObject(
            'Magento_Core_Block_Template_Context',
            array('urlBuilder' => $urlBuilder)
        );
        $link = $this->_objectManagerHelper->getObject(
            'Magento_Checkout_Block_Link',
            array(
                'context' => $context,
            )
        );
        $this->assertEquals($url . $path, $link->getHref());
    }

    /**
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($canOnepageCheckout, $isModuleOutputEnabled)
    {
        $helper = $this->getMockBuilder('Magento_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('canOnepageCheckout', 'isModuleOutputEnabled'))
            ->getMock();
        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();
        $layout->expects($this->once())->method('helper')->will($this->returnValue($helper));

        $moduleManager = $this->getMockBuilder('Magento_Core_Model_ModuleManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isOutputEnabled'))
            ->getMock();

        /** @var  Magento_Core_Block_Template_Context $context */
        $context = $this->_objectManagerHelper->getObject(
            'Magento_Core_Block_Template_Context',
            array('layout' => $layout)
        );

        /** @var Magento_Invitation_Block_Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento_Checkout_Block_Link',
            array(
                'context' => $context,
                'moduleManager' => $moduleManager
            )
        );
        $helper->expects($this->any())->method('canOnepageCheckout')->will($this->returnValue($canOnepageCheckout));
        $moduleManager->expects($this->any())
            ->method('isOutputEnabled')
            ->with('Magento_Checkout')
            ->will($this->returnValue($isModuleOutputEnabled));
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