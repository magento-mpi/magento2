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

class LinkTest extends \PHPUnit_Framework_TestCase
{

    public function testGetHref()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $helper = $this->getMockBuilder('Magento\Customer\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getAccountUrl'))
            ->getMock();

        $helperFactory = $this->getMockBuilder('Magento\Core\Model\Factory\Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        $layout = $this->getMockBuilder('Magento\Core\Model\Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $context = $objectManager->getObject(
            'Magento\Core\Block\Template\Context',
            array(
                'layout' => $layout,
                'helperFactory' => $helperFactory
            )
        );

        $block = $objectManager->getObject(
            'Magento\Customer\Block\Account\Link',
            array(
                'context' => $context,
            )
        );
        $helper->expects($this->any())->method('getAccountUrl')->will($this->returnValue('account url'));

        $this->assertEquals('account url', $block->getHref());
    }
}
