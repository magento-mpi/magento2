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
 * Test class for \Magento\Payment\Block\Info\Instructions
 */
class Magento_Payment_Block_Info_InstructionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Object
     */
    protected $_method;

    /**
     * @var \Magento\Payment\Model\Info
     */
    protected $_info;

    /**
     * @var \Magento\Payment\Block\Info\Instructions
     */
    protected $_instructions;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_method = new \Magento\Object();
        $this->_info = $objectManagerHelper->getObject('\Magento\Payment\Model\Info');
        $this->_instructions = $objectManagerHelper->getObject('\Magento\Payment\Block\Info\Instructions');

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
