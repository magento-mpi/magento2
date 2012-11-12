<?php
/**
 * Unit test for Mage_Index_Model_Process_FileFactory
 *
 * @copyright {}
 */
class Mage_Index_Model_Process_FileFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Bogus string to return from object manager's create() method
     */
    const CREATE_RESULT       = 'create_result';

    /**
     * Expected class name
     */
    const EXPECTED_CLASS_NAME = 'Mage_Index_Model_Process_File';

    /**
     * @var array
     */
    protected $_arguments = array(
        'key' => 'value'
    );

    public function testCreateFromArray()
    {
        $objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->will($this->returnCallback(array($this, 'verifyCreate')));

        $factory = new Mage_Index_Model_Process_FileFactory($objectManagerMock);
        $this->assertEquals(self::CREATE_RESULT, $factory->createFromArray($this->_arguments));
    }

    /**
     * Verify arguments passed to object manager's create() method
     *
     * @param string $className
     * @param array $arguments
     * @param boolean $isShared
     * @return string
     */
    public function verifyCreate($className, array $arguments, $isShared)
    {
        $this->assertEquals('Mage_Index_Model_Process_File', $className);
        $this->assertEquals($this->_arguments, $arguments);
        $this->assertFalse($isShared);

        return self::CREATE_RESULT;
    }
}
