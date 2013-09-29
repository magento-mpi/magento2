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
 * Test class for \Magento\Customer\Block\Account\RegisterLink
 */
class RegisterLinkTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testToHtml()
    {
        $context = $this->_objectManager->getObject('Magento\Core\Block\Template\Context');
        $session = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('isLoggedIn'))
            ->getMock();
        $session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        /** @var \Magento\Sales\Block\Guest\Link $link */
        $link = $this->_objectManager->getObject(
            'Magento\Customer\Block\Account\RegisterLink',
            array(
                'context' => $context,
                'session' => $session,
            )
        );

        $this->assertEquals('', $link->toHtml());
    }

    public function testGetHref()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $helper = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getRegisterUrl'))
            ->getMock();

        $helper->expects($this->any())->method('getRegisterUrl')->will($this->returnValue('register url'));

        $context = $this->_objectManager->getObject('Magento\Core\Block\Template\Context');

        $context->getHelperFactory()->expects($this->once())->method('get')->will($this->returnValue($helper));

        $block = $this->_objectManager->getObject(
            'Magento\Customer\Block\Account\RegisterLink',
            array(
                'context' => $context,
            )
        );
        $this->assertEquals('register url', $block->getHref());
    }
}
