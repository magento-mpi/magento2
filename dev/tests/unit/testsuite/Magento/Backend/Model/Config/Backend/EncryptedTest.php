<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Backend_EncryptedTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var Magento_Backend_Model_Config_Backend_Encrypted */
    protected $_model;

    public function setUp()
    {
        $contextMock = $this->getMock('Magento_Core_Model_Context', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $resourceMock = $this->getMock('Magento_Core_Model_Resource_Abstract',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName'),
            array(), '', false);
        $collectionMock = $this->getMock('Magento_Data_Collection_Db', array(), array(), '', false);
        $registry = $this->getMock('Magento_Core_Model_Registry');
        $this->_model = new Magento_Backend_Model_Config_Backend_Encrypted(
            $this->_helperMock, $contextMock, $registry, $resourceMock, $collectionMock
        );

    }

    public function testProcessValue()
    {
        $value = 'someValue';
        $result = 'some value from parent class';
        $this->_helperMock->expects($this->once())->method('decrypt')->with($value)->will($this->returnValue($result));
        $this->assertEquals($result, $this->_model->processValue($value));
    }
}
