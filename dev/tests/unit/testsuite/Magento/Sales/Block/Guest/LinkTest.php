<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Sales_Block_Guest_Link
 */
class Magento_Sales_Block_Guest_LinkTest extends PHPUnit_Framework_TestCase
{
    public function testToHtml()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $context = $objectManagerHelper->getObject('Magento_Core_Block_Template_Context');
        $session = $this->getMockBuilder('Magento_Customer_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('isLoggedIn'))
            ->getMock();
        $session->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        /** @var Magento_Sales_Block_Guest_Link $link */
        $link = $objectManagerHelper->getObject(
            'Magento_Sales_Block_Guest_Link',
            array(
                'context' => $context,
                'customerSession' => $session,
            )
        );

        $this->assertEquals('', $link->toHtml());
    }
}
