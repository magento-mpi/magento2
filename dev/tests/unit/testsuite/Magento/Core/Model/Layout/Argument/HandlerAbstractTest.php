<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Layout\Argument\HandlerAbstract
 */
class Magento_Core_Model_Layout_Argument_HandlerAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithValidObjectFactory()
    {
        $this->getMockForAbstractClass('\Magento\Core\Model\Layout\Argument\HandlerAbstract',
            array($this->getMock('Magento\ObjectManager', array(), array(), '', false)),
            '',
            true
        );
    }
}
