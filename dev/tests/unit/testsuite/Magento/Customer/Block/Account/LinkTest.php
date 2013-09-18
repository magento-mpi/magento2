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
 * Test class for Magento_Customer_Block_Account_Link
 */
class Magento_Customer_Block_Account_LinkTest extends PHPUnit_Framework_TestCase
{

    public function testGetHref()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $helper = $this->getMockBuilder('Magento_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getAccountUrl'))
            ->getMock();

        $helperFactory = $this->getMockBuilder('Magento_Core_Model_Factory_Helper')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $helperFactory->expects($this->any())->method('get')->will($this->returnValue($helper));

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $context = $objectManager->getObject(
            'Magento_Core_Block_Template_Context',
            array(
                'layout' => $layout,
                'helperFactory' => $helperFactory
            )
        );

        $block = $objectManager->getObject(
            'Magento_Customer_Block_Account_Link',
            array(
                'context' => $context,
            )
        );
        $helper->expects($this->any())->method('getAccountUrl')->will($this->returnValue('account url'));

        $this->assertEquals('account url', $block->getHref());
    }
}
