<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Payment_Block_Info_ContainerAbstract
 */
class Mage_Payment_Block_Info_ContainerAbstractTest extends Magento_Test_TestCase_ObjectManager
{
    public function testSetInfoTemplate()
    {
        $block = $this->getMock('Mage_Payment_Block_Info_ContainerAbstract', array('getChildBlock', 'getPaymentInfo'),
            array(), '', false);
        $paymentInfo = $this->getModel('Mage_Payment_Model_Info');
        $methodInstance = $this->getModel('Mage_Payment_Model_Method_Checkmo');
        $paymentInfo->setMethodInstance($methodInstance);
        $block->expects($this->atLeastOnce())
            ->method('getPaymentInfo')
            ->will($this->returnValue($paymentInfo));

        $childBlock = $this->getBlock('Mage_Core_Block_Template');
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('payment.info.checkmo')
            ->will($this->returnValue($childBlock));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlock->getTemplate());
        $block->setInfoTemplate('checkmo', $template);
        $this->assertEquals($template, $childBlock->getTemplate());
    }
}
