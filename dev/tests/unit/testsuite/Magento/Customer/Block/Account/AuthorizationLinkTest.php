<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

/**
 * Test class for \Magento\Customer\Block\Account\AuthorizationLink
 */
class AuthorizationLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Block\Account\AuthorizationLink
     */
    protected $_block;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_session = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('isLoggedIn'))
            ->getMock();
        $this->_helper = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getLogoutUrl', 'getLoginUrl'))
            ->getMock();

        $context = $this->_objectManager->getObject('Magento\View\Element\Template\Context');
        $this->_block = $this->_objectManager->getObject(
            'Magento\Customer\Block\Account\AuthorizationLink',
            array(
                'context' => $context,
                'session' => $this->_session,
                'customerHelper' => $this->_helper,
            )
        );
    }

    public function testGetLabelLoggedIn()
    {
        $this->_session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        $this->assertEquals('Log Out', $this->_block->getLabel());
    }

    public function testGetLabelLoggedOut()
    {
        $this->_session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->assertEquals('Log In', $this->_block->getLabel());
    }

    public function testGetHrefLoggedIn()
    {
        $this->_session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        $this->_helper->expects($this->once())->method('getLogoutUrl')->will($this->returnValue('logout url'));

        $this->assertEquals('logout url', $this->_block->getHref());
    }

    public function testGetHrefLoggedOut()
    {
        $this->_session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->_helper->expects($this->once())->method('getLoginUrl')->will($this->returnValue('login url'));

        $this->assertEquals('login url', $this->_block->getHref());
    }
}
