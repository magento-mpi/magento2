<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Tools\Migration\Acl\Db;


require_once realpath(__DIR__ . '/../../../../../../../../../') . '/tools/Magento/Tools/Migration/Acl/Db/Writer.php';
class WriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\Acl\Db\Writer
     */
    protected $_model;

    /**
     * DB adapter
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    protected function setUp()
    {
        $this->_adapterMock = $this->getMockForAbstractClass(
            'Zend_Db_Adapter_Abstract',
            array(),
            '',
            false,
            false,
            false,
            array('update')
        );
        $this->_model = new \Magento\Tools\Migration\Acl\Db\Writer($this->_adapterMock, 'dummy');
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_adapterMock);
    }

    public function testUpdate()
    {
        $this->_adapterMock->expects(
            $this->once()
        )->method(
            'update'
        )->with(
            'dummy',
            array('resource_id' => 'new'),
            array('resource_id = ?' => 'old')
        );
        $this->_model->update('old', 'new');
    }
}
