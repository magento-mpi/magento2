<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Session */
    protected $_session;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Helper\Data */
    protected $_helper;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Page\Block\Template\Links */
    protected $_targetBlock;

    /** @var \Magento\Customer\Block\Account\Link */
    protected $_block;

    public function setUp()
    {
        $this->_session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);

        $this->_helper = $this->getMock('Magento\Customer\Helper\Data', array(), array(), '', false);

        $helperFactory = $this->getMock('Magento\Core\Model\Factory\Helper', array(), array(), '', false);
        $helperFactory->expects($this->any())
            ->method('get')
            ->with('Magento\Customer\Helper\Data')
            ->will($this->returnValue($this->_helper));

        $this->_targetBlock = $this->getMock('Magento\Page\Block\Template\Links', array(), array(), '', false);

        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $layout->expects($this->any())
            ->method('getBlock')
            ->with('target_block')
            ->will($this->returnValue($this->_targetBlock));

        $context = $this->getMock('Magento\Core\Block\Context', array(), array(), '', false);
        $context->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactory));
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->_block = new \Magento\Customer\Block\Account\Link($context, $this->_session);
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
