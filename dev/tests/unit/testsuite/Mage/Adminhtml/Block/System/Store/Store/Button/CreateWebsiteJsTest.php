<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_System_Store_Store_Button_CreateWebsiteJsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param bool $isRestricted
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($isRestricted)
    {
        list($block, $limitation) = $this->_createBlockAndLimitation();

        $helper = new Magento_Test_Helper_ProxyTesting;
        $result = $helper->invokeWithExpectations($block, $limitation, 'isCreateRestricted', array(), $isRestricted);
        $this->assertEquals($isRestricted, $result);
    }

    /**
     * @return array
     */
    public static function isCreateRestrictedDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    public function testGetCreateRestrictedMessage()
    {
        list($block, $limitation) = $this->_createBlockAndLimitation();

        $helper = new Magento_Test_Helper_ProxyTesting;
        $expectedMessage = 'restricted_message';
        $result = $helper->invokeWithExpectations($block, $limitation, 'getCreateRestrictedMessage', array(),
            $expectedMessage);
        $this->assertEquals($expectedMessage, $result);
    }

    /**
     * Create block and limitation model, link them together
     *
     * @return array
     */
    protected function _createBlockAndLimitation()
    {
        $limitation = $this->getMock('Mage_Core_Model_Website_Limitation', array(), array(), '', false);

        $block = $this->getMock('Mage_Adminhtml_Block_System_Store_Store_Button_CreateWebsiteJs',
            array('_getLimitation'), array(), '', false);
        $block->expects($this->any())
            ->method('_getLimitation')
            ->will($this->returnValue($limitation));

        return array($block, $limitation);
    }

    public function testGetCreateUrl()
    {
        // Plan
        $expectedUrl = 'http://example.com/new_website/';

        $urlBuilder = $this->getMock('Mage_Core_Model_UrlInterface', array(), array(), '', false);
        $urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/newWebsite')
            ->will($this->returnValue($expectedUrl));

        $context = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($urlBuilder));

        // Do and check
        $block = new Mage_Adminhtml_Block_System_Store_Store_Button_CreateWebsiteJs($context);
        $result = $block->getCreateUrl();
        $this->assertEquals($expectedUrl, $result);
    }
}
