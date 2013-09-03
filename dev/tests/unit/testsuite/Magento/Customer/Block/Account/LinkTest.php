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
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $helper = $this->getMockBuilder('Magento_Customer_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getAccountUrl'))
            ->getMock();

        $layout = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->setMethods(array('helper'))
            ->getMock();

        $layout->expects($this->once())->method('helper')->will($this->returnValue($helper));

        $context = $objectManager->getObject(
            'Magento_Core_Block_Template_Context',
            array('layout' => $layout)
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
