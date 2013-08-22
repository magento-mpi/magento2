<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Payment_Block_Info_ContainerAbstract
 */
class Magento_Payment_Block_Info_ContainerAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSetInfoTemplate()
    {
        $block = $this->getMock('Magento_Payment_Block_Info_ContainerAbstract',
            array('getChildBlock', 'getPaymentInfo'), array(), '', false);
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $paymentInfo = $objectManagerHelper->getObject('Magento_Payment_Model_Info');
        $methodInstance = $objectManagerHelper->getObject('Magento_Payment_Model_Method_Checkmo');
        $paymentInfo->setMethodInstance($methodInstance);
        $block->expects($this->atLeastOnce())
            ->method('getPaymentInfo')
            ->will($this->returnValue($paymentInfo));

        $childBlock = $objectManagerHelper->getObject('Magento_Core_Block_Template');
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
