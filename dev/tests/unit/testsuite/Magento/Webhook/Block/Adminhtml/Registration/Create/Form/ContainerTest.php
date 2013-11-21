<?php
/**
 * \Magento\Webhook\Block\Adminhtml\Registration\Create\Form\Container
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Block\Adminhtml\Registration\Create\Form;

class ContainerTest extends \PHPUnit_Framework_TestCase
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
        $this->_urlBuilder = $this->getMock('Magento\UrlInterface');

        /** @var  $coreData \Magento\Core\Helper\Data */
        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);

        $registry = $this->getMock('Magento\Core\Model\Registry', array('registry'), array(), '', false);
        $registry->expects($this->once())
            ->method('registry')
            ->with('current_subscription')
            ->will($this->returnValue($this->_subscription));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject('\Magento\Webhook\Block\Adminhtml\Registration\Create\Form\Container',
            array(
                'coreData' => $coreData,
                'registry' => $registry,
                'urlBuilder' => $this->_urlBuilder
            )
        );
    }

    public function testGetAcceptUrl()
    {
        $url = 'example.url.com/id/333';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/register', array('id' => 333))
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->_block->getSubmitUrl());
    }

    public function testGetSubscriptionName()
    {
        $this->assertEquals($this->_subscription['name'], $this->_block->getSubscriptionName());
    }
}
