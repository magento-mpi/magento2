<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/Acl/Db/Writer.php';

class Magento_Test_Tools_Migration_Acl_Db_WriterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tools_Migration_Acl_Db_Writer
     */
    protected $_model;

    /**
     * DB adapter
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    public function setUp()
    {
        $this->_adapterMock = $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract',
            array(),
            '',
            false,
            false,
            false,
            array('update')
        );
        $this->_model = new Magento_Tools_Migration_Acl_Db_Writer($this->_adapterMock, 'dummy');
    }

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_adapterMock);
    }

    public function testUpdate()
    {
        $this->_adapterMock->expects($this->once())
            ->method('update')->with('dummy', array('resource_id' => 'new'), array('resource_id = ?' => 'old'));
        $this->_model->update('old', 'new');
    }
}

