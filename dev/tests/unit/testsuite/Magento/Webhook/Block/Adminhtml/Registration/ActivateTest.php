<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Activate
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration;

class ActivateTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Webhook\Block\Adminhtml\Registration\Activate */
    private $_block;

    /** @var \Magento\Core\Model\Url */
    private $_urlBuilder;

    /** @var array  */
    private $_subscription = array(
        'subscription_id' => 333,
        'name' => 'test_subscription',
        'topics' => array('customer/created', 'customer/updated')
    );

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento\Core\Model\Url', array('getUrl'), array(), '', false);

        /** @var  $coreData \Magento\Core\Helper\Data */
        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);

        /** @var \Magento\Core\Block\Template\Context $context */
        $context = $this->getMock('Magento\Backend\Block\Template\Context', array('getUrlBuilder'), array(), '', false);
        $context->expects($this->once())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->_urlBuilder));

        $registry = $this->getMock('Magento\Core\Model\Registry', array('registry'), array(), '', false);
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_subscription')
            ->will($this->returnValue($this->_subscription));
        $this->_block = new \Magento\Webhook\Block\Adminhtml\Registration\Activate($coreData, $context, $registry);
    }

    public function testGetAcceptUrl()
    {
        $url = 'example.url.com/id/333';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/accept', array('id' => 333))
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->_block->getAcceptUrl());
    }

    public function testGetSubscriptionName()
    {
        $this->assertEquals($this->_subscription['name'], $this->_block->getSubscriptionName());
    }

    public function testGetSubscriptionTopics()
    {
        $this->assertEquals($this->_subscription['topics'], $this->_block->getSubscriptionTopics());
    }
}
