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

    /** @var \Magento\Backend\Model\Config\Backend\Encrypted */
    protected $_model;

    protected function setUp()
    {
        $contextMock = $this->getMock('Magento\Core\Model\Context', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $resourceMock = $this->getMock('Magento\Core\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName'),
            array(), '', false);
        $collectionMock = $this->getMock('Magento\Data\Collection\Db', array(), array(), '', false);
        $registry = $this->getMock('Magento\Core\Model\Registry');
        $storeManager = $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);
        $this->_model = new \Magento\Backend\Model\Config\Backend\Encrypted(
            $this->_helperMock, $contextMock, $registry, $storeManager, $coreConfig, $resourceMock, $collectionMock
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
