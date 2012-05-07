<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Block_Info_InstructionsTest extends PHPUnit_Framework_TestCase
{
    public function testGetInstructions()
    {
        $paymentMethod = new Varien_Object;
        $paymentInfo = new Mage_Payment_Model_Info;
        $paymentInfo->setMethodInstance($paymentMethod);

        $object = new Mage_Payment_Block_Info_Instructions;
        $object->setInfo($paymentInfo);

        $this->assertNull($object->getInstructions());

        //Use payment method model in case instructions in info model are empty
        $testInstruction = 'first test';
        $paymentMethod->setInstructions($testInstruction);
        $this->assertEquals($testInstruction, $object->getInstructions());

        $object = new Mage_Payment_Block_Info_Instructions;
        $object->setInfo($paymentInfo);

        $testInstruction = 'second test';
        $paymentInfo->setAdditionalInformation('instructions', $testInstruction);
        $this->assertEquals($testInstruction, $object->getInstructions());
    }
}
