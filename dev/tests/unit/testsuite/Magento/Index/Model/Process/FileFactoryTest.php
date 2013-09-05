<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unit test for Magento_Index_Model_Process_FileFactory
 */
class Magento_Index_Model_Process_FileFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Bogus string to return from object manager's create() method
     */
    const CREATE_RESULT       = 'create_result';

    /**
     * Expected class name
     */
    const EXPECTED_CLASS_NAME = 'Magento_Index_Model_Process_File';

    /**
     * @var array
     */
    protected $_arguments = array(
        'key' => 'value'
    );

    public function testCreate()
    {
        $objectManagerMock = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with(self::EXPECTED_CLASS_NAME, $this->_arguments)
            ->will($this->returnValue(self::CREATE_RESULT));

        $factory = new Magento_Index_Model_Process_FileFactory($objectManagerMock);
        $this->assertEquals(self::CREATE_RESULT, $factory->create($this->_arguments));
    }
}
