<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Block_Account_LinkTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Customer_Block_Account_Link */
    protected $_block;

    /**
     * @param bool $isLoggedIn
     * @param string $expectedUrlMethod
     * @dataProvider removeAuthLinkDataProvider
     */
    public function testRemoveAuthLink($isLoggedIn, $expectedUrlMethod)
    {
        $session = $this->getMock('Mage_Customer_Model_Session', array(), array(), '', false);
        $session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue($isLoggedIn));

        $helper = $this->getMock('Mage_Customer_Helper_Data', array(), array(), '', false);
        $helper->expects($this->once())
            ->method($expectedUrlMethod)
            ->will($this->returnValue('composed_url'));

        $helperFactory = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false);
        $helperFactory->expects($this->once())
            ->method('get')
            ->with('Mage_Customer_Helper_Data')
            ->will($this->returnValue($helper));

        $targetBlock = $this->getMock('Mage_Page_Block_Template_Links', array(), array(), '', false);
        $targetBlock->expects($this->once())
            ->method('removeLinkByUrl')
            ->with('composed_url');

        $layout = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('target_block')
            ->will($this->returnValue($targetBlock));

        $context = $this->getMock('Mage_Core_Block_Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactory));
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $block = new Mage_Customer_Block_Account_Link($context, $session);
        $result = $block->removeAuthLink('target_block');
        $this->assertSame($block, $result);
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
}
