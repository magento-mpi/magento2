<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Import_SourceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Import\SourceAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\SourceAbstract', array(array(
            'key1', 'key2', 'key3'
        )));
    }

    /**
     * @param array $argument
     * @dataProvider constructExceptionDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testConstructException($argument)
    {
        $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\SourceAbstract', array($argument));
    }

    /**
     * @return array
     */
    public function constructExceptionDataProvider()
    {
        return array(
            'empty column names' => array(array()),
            'duplicate column names' => array(array('1', '2', '1')),
        );
    }

    public function testGetColNames()
    {
        $this->assertSame(array('key1', 'key2', 'key3'), $this->_model->getColNames());
    }

    public function testIteratorInterface()
    {
        $this->assertSame(array('key1' => '', 'key2' => '', 'key3' => ''), $this->_model->current());
        $this->assertSame(-1, $this->_model->key());
        $this->assertFalse($this->_model->valid());

        $this->_model->expects($this->exactly(4))->method('_getNextRow')->will($this->onConsecutiveCalls(
            array(1, 2, 3), array(4, 5), array(6, 7, 8), false
        ));
        $data = array();
        foreach ($this->_model as $key => $value) {
            $data[$key] = $value;
        }
        $this->assertSame(array(
            array('key1' => 1, 'key2' => 2, 'key3' => 3),
            array('key1' => 4, 'key2' => 5, 'key3' => ''),
            array('key1' => 6, 'key2' => 7, 'key3' => 8),
        ), $data);
    }

    public function testSeekableInterface()
    {
        $this->assertSame(-1, $this->_model->key());
        $this->_model->seek(-1);
        $this->assertSame(-1, $this->_model->key());

        $this->_model->expects($this->any())->method('_getNextRow')->will($this->onConsecutiveCalls(
            array(1, 2, 3), array(4, 5), array(6, 7, 8),
            array(1, 2, 3), array(4, 5)
        ));
        $this->_model->seek(2);
        $this->assertSame(array('key1' => 6, 'key2' => 7, 'key3' => 8), $this->_model->current());
        $this->_model->seek(1);
        $this->assertSame(array('key1' => 4, 'key2' => 5, 'key3' => ''), $this->_model->current());
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testSeekableInterfaceException()
    {
        $this->_model->seek(0);
    }
}
