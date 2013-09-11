<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_Reader_DefaultReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Section\Reader\DefaultReader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_initialConfigMock = $this->getMock('Magento\Core\Model\Config\Initial', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_appStateMock = $this->getMock('Magento\Core\Model\App\State', array(), array(), '', false);
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_model = new \Magento\Core\Model\Config\Section\Reader\DefaultReader(
            $this->_initialConfigMock,
            new \Magento\Core\Model\Config\Section\Converter(),
            $this->_collectionFactory,
            $this->_appStateMock
        );
    }

    public function testRead()
    {
        $this->_initialConfigMock->expects($this->any())
            ->method('getDefault')
            ->will($this->returnValue(array(
                'config' => array('key1' => 'default_value1', 'key2' => 'default_value2'),
            )));
        $this->_collectionFactory->expects($this->once())
            ->method('create')
            ->with(array('scope' => 'default'))
            ->will($this->returnValue(array(
                new \Magento\Object(array('path' => 'config/key1', 'value' => 'default_db_value1')),
                new \Magento\Object(array('path' => 'config/key3', 'value' => 'default_db_value3')),
            )));
        $expectedData = array(
            'config' => array(
                'key1' => 'default_db_value1',
                'key2' => 'default_value2',
                'key3' => 'default_db_value3'
            ),
        );
        $this->assertEquals($expectedData, $this->_model->read());
    }
}
