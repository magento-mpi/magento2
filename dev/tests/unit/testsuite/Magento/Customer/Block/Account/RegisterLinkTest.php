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

/**
 * Test class for Magento_Customer_Block_Account_RegisterLink
 */
class Magento_Customer_Block_Account_RegisterLinkTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
    }

    public function testToHtml()
    {
        $context = $this->_objectManager->getObject('Magento_Core_Block_Template_Context');
        $session = $this->getMockBuilder('Magento_Customer_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('isLoggedIn'))
            ->getMock();
        $session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        /** @var Magento_Sales_Block_Guest_Link $link */
        $link = $this->_objectManager->getObject(
            'Magento_Customer_Block_Account_RegisterLink',
            array(
                'context' => $context,
                'session' => $session,
            )
        );

        $this->assertEquals('', $link->toHtml());
    }

    public function testGetHref()
    {
        $this->_objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $helper = $this->getMockBuilder('Magento_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getRegisterUrl'))
            ->getMock();

        $helper->expects($this->any())->method('getRegisterUrl')->will($this->returnValue('register url'));

        $context = $this->_objectManager->getObject('Magento_Core_Block_Template_Context');

        $context->getHelperFactory()->expects($this->once())->method('get')->will($this->returnValue($helper));

        $block = $this->_objectManager->getObject(
            'Magento_Customer_Block_Account_RegisterLink',
            array(
                'context' => $context,
            )
        );
        $this->assertEquals('register url', $block->getHref());
    }
}
