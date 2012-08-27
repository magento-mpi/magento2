<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Layout_Argument_Processor_TypeAbstract
 */
class Mage_Core_Model_Layout_Argument_Processor_AbstractTypeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithoutObjectFactory()
    {
        $this->getMockForAbstractClass('Mage_Core_Model_Layout_Argument_Processor_TypeAbstract',
            array(array('someParam' => true)),
            '',
            true
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithInvalidObjectFactory()
    {
        $this->getMockForAbstractClass('Mage_Core_Model_Layout_Argument_Processor_TypeAbstract',
            array(array('objectFactory' => new StdClass())),
            '',
            true
        );
    }

    public function testConstructWithValidObjectFactory()
    {
        $this->getMockForAbstractClass('Mage_Core_Model_Layout_Argument_Processor_TypeAbstract',
            array(array('objectFactory' => $this->getMock('Mage_Core_Model_Config', array(), array(), '', false))),
            '',
            true
        );
    }
}
