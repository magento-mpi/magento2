<?php
/**
 * Magento_Webhook_Block_Adminhtml_Registration_Create_Form_Container
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Block_Adminhtml_Registration_Create_Form_ContainerTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_Webhook_Block_Adminhtml_Registration_Activate */
    private $_block;

    /** @var Magento_Core_Model_Url */
    private $_urlBuilder;

    /** @var array  */
    private $_subscription = array(
        'subscription_id' => 333,
        'name' => 'test_subscription',
        'topics' => array('customer/created', 'customer/updated')
    );

    public function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento_Core_Model_Url', array('getUrl'), array(), '', false);
        /** @var Magento_Core_Block_Template_Context $context */
        $context = $this->getMock('Magento_Backend_Block_Template_Context', array('getUrlBuilder'), array(), '', false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->_urlBuilder));

        $registry = $this->getMock('Magento_Core_Model_Registry', array('registry'), array(), '', false);
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_subscription')
            ->will($this->returnValue($this->_subscription));
        $this->_block = new Magento_Webhook_Block_Adminhtml_Registration_Create_Form_Container($context, $registry);
    }

    public function testGetAcceptUrl()
    {
        $url = 'example.url.com/id/333';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/register', array('id' => 333))
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->_block->getSubmitUrl());
    }

    public function testGetSubscriptionName()
    {
        $this->assertEquals($this->_subscription['name'], $this->_block->getSubscriptionName());
    }
}
