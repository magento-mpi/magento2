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
 * Test class for Magento_Payment_Block_Info_Instructions
 */
class Magento_Payment_Block_Info_InstructionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Object
     */
    protected $_method;

    /**
     * @var Magento_Payment_Model_Info
     */
    protected $_info;

    /**
     * @var Magento_Payment_Block_Info_Instructions
     */
    protected $_instructions;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_method = new Magento_Object();
        $this->_info = $objectManagerHelper->getObject('Magento_Payment_Model_Info');
        $this->_instructions = $objectManagerHelper->getObject('Magento_Payment_Block_Info_Instructions');

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
