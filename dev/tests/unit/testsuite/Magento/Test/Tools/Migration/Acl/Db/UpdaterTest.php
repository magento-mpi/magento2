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

require_once realpath(__DIR__ . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Updater.php';

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_writerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    /**
     * @var array
     */
    protected $_map = array();

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\Tools\Migration\Acl\Db\Reader', array(), array(), '', false);
        $this->_readerMock->expects($this->once())->method('fetchAll')->will($this->returnValue(array(
            'oldResource1' => 1,
            'oldResource2' => 2,
            'Test::newResource3'  => 3,
            'additionalResource'  => 4,
        )));

        $this->_map = array(
            "oldResource1"  => "Test::newResource1",
            "oldResource2"  => "Test::newResource2",
            "oldResource3"  => "Test::newResource3",
            "oldResource4"  => "Test::newResource4",
            "oldResource5"  => "Test::newResource5"
        );

        $this->_writerMock = $this->getMock('Magento\Tools\Migration\Acl\Db\Writer', array(), array(), '', false);
        $this->_loggerMock = $this->getMockForAbstractClass(
            'Magento\Tools\Migration\Acl\Db\AbstractLogger', array(), '', false, false, false, array('add')
        );
    }

    public function testMigrateInPreviewModeDoesntWriteToDb()
    {
        $model = new \Magento\Tools\Migration\Acl\Db\Updater(
            $this->_readerMock, $this->_writerMock, $this->_loggerMock, null
        );

        $this->_writerMock->expects($this->never())->method('update');

        $this->_loggerMock->expects($this->at(0))->method('add')->with('oldResource1', 'Test::newResource1', 1);
        $this->_loggerMock->expects($this->at(1))->method('add')->with('oldResource2', 'Test::newResource2', 2);
        $this->_loggerMock->expects($this->at(2))->method('add')->with(null, 'Test::newResource3', 3);
        $this->_loggerMock->expects($this->at(3))->method('add')->with('additionalResource', null, 4);

        $model->migrate($this->_map);
    }

    public function testMigrateInRealModeWritesToDb()
    {
        $model = new \Magento\Tools\Migration\Acl\Db\Updater(
            $this->
                _readerMock, $this->_writerMock, $this->_loggerMock, \Magento\Tools\Migration\Acl\Db\Updater::WRITE_MODE
        );

        $this->_writerMock->expects($this->at(0))->method('update')->with('oldResource1', 'Test::newResource1');
        $this->_writerMock->expects($this->at(1))->method('update')->with('oldResource2', 'Test::newResource2');

        $this->_loggerMock->expects($this->at(0))->method('add')->with('oldResource1', 'Test::newResource1', 1);
        $this->_loggerMock->expects($this->at(1))->method('add')->with('oldResource2', 'Test::newResource2', 2);
        $this->_loggerMock->expects($this->at(2))->method('add')->with(null, 'Test::newResource3', 3);
        $this->_loggerMock->expects($this->at(3))->method('add')->with('additionalResource', null, 4);

        $model->migrate($this->_map);
    }
}

