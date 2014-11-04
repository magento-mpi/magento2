<?php
/**
 * {license_notice}
 *
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

    /**
     * @param bool $isAuthenticated
     * @param bool $isRegistrationAllowed
     * @param bool $result
     * @dataProvider dataProviderToHtml
     * @return void
     */
    public function testToHtml($isAuthenticated, $isRegistrationAllowed, $result)
    {
        $context = $this->_objectManager->getObject('Magento\Framework\View\Element\Template\Context');

        $httpContext = $this->getMockBuilder('Magento\Framework\App\Http\Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getValue'))
            ->getMock();
        $httpContext->expects($this->any())
            ->method('getValue')
            ->with(\Magento\Customer\Helper\Data::CONTEXT_AUTH)
            ->will($this->returnValue($isAuthenticated));

        $helperMock = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('isRegistrationAllowed'))
            ->getMock();
        $helperMock->expects($this->any())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue($isRegistrationAllowed));

        /** @var \Magento\Customer\Block\Account\RegisterLink $link */
        $link = $this->_objectManager->getObject(
            'Magento\Customer\Block\Account\RegisterLink',
            array(
                'context' => $context,
                'httpContext' => $httpContext,
                'customerHelper' => $helperMock,
            )
        );

        $this->assertEquals($result, $link->toHtml() === '');
    }

    /**
     * @return array
     */
    public function dataProviderToHtml()
    {
        return array(
            array(true, true, true),
            array(false, false, true),
            array(true, false, true),
            array(false, true, false),
        );
    }

    public function testGetHref()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $helper = $this->getMockBuilder(
            'Magento\Customer\Model\Url'
        )->disableOriginalConstructor()->setMethods(
            array('getRegisterUrl')
        )->getMock();

        $helper->expects($this->any())->method('getRegisterUrl')->will($this->returnValue('register url'));

        $context = $this->_objectManager->getObject('Magento\Framework\View\Element\Template\Context');

        $block = $this->_objectManager->getObject(
            'Magento\Customer\Block\Account\RegisterLink',
            array('context' => $context, 'customerUrl' => $helper)
        );
        $this->assertEquals('register url', $block->getHref());
    }
}
