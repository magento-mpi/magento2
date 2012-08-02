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
    protected $_method;
    protected $_info;
    protected $_instructions;

    protected function setUp()
    {
        $this->_method = new Varien_Object();
        $this->_info = new Mage_Payment_Model_Info();
        $this->_instructions = new Mage_Payment_Block_Info_Instructions();

        $this->_info->setMethodInstance($this->_method);
        $this->_instructions->setInfo($this->_info);
    }

    public function testGetInstructionsEmpty()
    {
        $this->assertNull($this->_instructions->getInstructions());
    }

    public function testGetInstructions()
    {
        //Use payment method model in case instructions in info model are empty
        $testInstruction = 'first test';
        $this->_method->setInstructions($testInstruction);
        $this->assertEquals($testInstruction, $this->_instructions->getInstructions());

        $this->_instructions = new Mage_Payment_Block_Info_Instructions();
        $this->_instructions->setInfo($this->_info);

        $testInstruction = 'second test';
        $this->_info->setAdditionalInformation('instructions', $testInstruction);
        $this->assertEquals($testInstruction, $this->_instructions->getInstructions());
    }
}
