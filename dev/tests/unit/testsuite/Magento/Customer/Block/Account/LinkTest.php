<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Block_Account_LinkTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Customer_Model_Session */
    protected $_session;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Customer_Helper_Data */
    protected $_helper;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Page_Block_Template_Links */
    protected $_targetBlock;

    /** @var Magento_Customer_Block_Account_Link */
    protected $_block;

    public function setUp()
    {
        $this->_session = $this->getMock('Magento_Customer_Model_Session', array(), array(), '', false);

        $this->_helper = $this->getMock('Magento_Customer_Helper_Data', array(), array(), '', false);

        $helperFactory = $this->getMock('Magento_Core_Model_Factory_Helper', array(), array(), '', false);
        $helperFactory->expects($this->any())
            ->method('get')
            ->with('Magento_Customer_Helper_Data')
            ->will($this->returnValue($this->_helper));

        $this->_targetBlock = $this->getMock('Magento_Page_Block_Template_Links', array(), array(), '', false);

        $layout = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
        $layout->expects($this->any())
            ->method('getBlock')
            ->with('target_block')
            ->will($this->returnValue($this->_targetBlock));

        $context = $this->getMock('Magento_Core_Block_Context', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactory));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->_block = new Magento_Customer_Block_Account_Link($context, $this->_session);
    }

    /**
     * @param bool $isLoggedIn
     * @param string $expectedUrlMethod
     * @dataProvider removeAuthLinkDataProvider
     */
    public function testRemoveAuthLink($isLoggedIn, $expectedUrlMethod)
    {
        $this->_session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue($isLoggedIn));

        $this->_helper->expects($this->once())
            ->method($expectedUrlMethod)
            ->will($this->returnValue('composed_url'));

        $this->_targetBlock->expects($this->once())
            ->method('removeLinkByUrl')
            ->with('composed_url');

        $result = $this->_block->removeAuthLink('target_block');
        $this->assertSame($this->_block, $result);
    }

    /**
     * @return array
     */
    public static function removeAuthLinkDataProvider()
    {
        return array(
            'Log In url' => array(
                true,
                'getLogoutUrl',
            ),
            'Log Out url' => array(
                false,
                'getLoginUrl',
            ),
        );
    }

    public function testRemoveRegisterLink()
    {
        $this->_helper->expects($this->once())
            ->method('getRegisterUrl')
            ->will($this->returnValue('register_url'));

        $this->_targetBlock->expects($this->once())
            ->method('removeLinkByUrl')
            ->with('register_url');

        $result = $this->_block->removeRegisterLink('target_block');
        $this->assertSame($this->_block, $result);
    }
}
