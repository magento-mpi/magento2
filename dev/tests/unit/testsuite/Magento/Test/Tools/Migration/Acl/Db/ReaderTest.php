<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Tools\Migration\Acl\Db;

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Reader.php';

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Reader
     */
    protected $_model;

    /**
     * DB adapter
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    public function setUp()
    {
        $this->_adapterMock = $this->getMockForAbstractClass(
            'Zend_Db_Adapter_Abstract',
            array(),
            '',
            false,
            false,
            false,
            array('select', 'fetchPairs')
        );
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Reader($this->_adapterMock, 'dummy');
    }

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_adapterMock);
    }

    public function testFetchAll()
    {
        $expected = array(
            'all' => 10,
            'catalog' => 100,
        );
        $selectMock = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $this->_adapterMock->expects($this->once())->method('select')->will($this->returnValue($selectMock));
        $selectMock->expects($this->once())->method('from')->will($this->returnSelf());
        $selectMock->expects($this->once())->method('columns')->will($this->returnSelf());
        $selectMock->expects($this->once())->method('group')->will($this->returnSelf());
        $this->_adapterMock->expects($this->once())->method('fetchPairs')->will($this->returnValue($expected));
        $actual = $this->_model->fetchAll();
        $this->assertEquals($expected, $actual);
    }
}

