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
 * Test class for Mage_Core_Model_Layout_Argument_HandlerAbstract
 */
class Mage_Core_Model_Layout_Argument_HandlerAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithValidObjectFactory()
    {
        $this->getMockForAbstractClass('Mage_Core_Model_Layout_Argument_HandlerAbstract',
            array($this->getMock('Magento_ObjectManager', array(), array(), '', false)),
            '',
            true
        );
    }
}
