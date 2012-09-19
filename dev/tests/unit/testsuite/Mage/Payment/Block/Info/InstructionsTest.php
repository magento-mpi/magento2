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

class Mage_Payment_Block_Info_InstructionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Object
     */
    protected $_method;

    /**
     * @var Mage_Payment_Model_Info
     */
    protected $_info;

    /**
     * @var Mage_Payment_Block_Info_Instructions
     */
    protected $_instructions;

    protected function setUp()
    {
        $this->_method = new Varien_Object();
        $this->_info = new Mage_Payment_Model_Info();
        $this->_instructions = new Mage_Payment_Block_Info_Instructions();

        $this->_info->setMethodInstance($this->_method);
        $this->_instructions->setInfo($this->_info);
    }

    public function testGetInstructionsSetInstructions()
    {
        $this->assertNull($this->_instructions->getInstructions());
        $testInstruction = 'first test';
        $this->_method->setInstructions($testInstruction);
        $this->assertEquals($testInstruction, $this->_instructions->getInstructions());
    }

    public function testGetInstructionsSetInformation()
    {
        $this->assertNull($this->_instructions->getInstructions());
        $testInstruction = 'second test';
        $this->_info->setAdditionalInformation('instructions', $testInstruction);
        $this->assertEquals($testInstruction, $this->_instructions->getInstructions());
    }
}
