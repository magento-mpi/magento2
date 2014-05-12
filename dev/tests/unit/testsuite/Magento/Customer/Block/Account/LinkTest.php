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
    public function testGetHref()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $helper = $this->getMockBuilder(
            'Magento\Customer\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
            array('getAccountUrl')
        )->getMock();
        $layout = $this->getMockBuilder(
            'Magento\Framework\View\Layout'
        )->disableOriginalConstructor()->setMethods(
            array('helper')
        )->getMock();


        $block = $objectManager->getObject(
            'Magento\Customer\Block\Account\Link',
            array('layout' => $layout, 'customerHelper' => $helper)
        );
        $helper->expects($this->any())->method('getAccountUrl')->will($this->returnValue('account url'));

        $this->assertEquals('account url', $block->getHref());
    }
}
