<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_exportMock;

    /**
     * @var Saas_ImportExport_Model_Export
     */
    protected $_exportModel;

    protected function setUp()
    {
        $this->_exportMock = $this->getMock('Saas_ImportExport_Model_Export', array('_init', '_paginateCollection',
            '_isCanExport', '_finishExportSuccess', '_export'),
            array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_exportModel = $objectManager->getObject('Saas_ImportExport_Model_Export', array());
    }

    public function testExportIsProcess()
    {
        $this->_exportMock->expects($this->once())->method('_init');
        $this->_exportMock->expects($this->once())->method('_paginateCollection');
        $this->_exportMock->expects($this->once())->method('_isCanExport')->will($this->returnValue(true));
        $this->_exportMock->expects($this->once())->method('_export');
        $this->_exportMock->export(array());
    }

    public function testExportIsNotProcess()
    {
        $this->_exportMock->expects($this->once())->method('_init');
        $this->_exportMock->expects($this->once())->method('_paginateCollection');
        $this->_exportMock->expects($this->once())->method('_isCanExport')->will($this->returnValue(false));
        $this->_exportMock->expects($this->once())->method('_finishExportSuccess');
        $this->_exportMock->export(array());
    }

    public function testGetIsFinished()
    {
        $this->assertFalse($this->_exportModel->isFinished());
        $this->_callFinishExportMethod();
        $this->assertTrue($this->_exportModel->isFinished());
    }

    protected function _callFinishExportMethod() {
        $class = new ReflectionClass('Saas_ImportExport_Model_Export');
        $method = $class->getMethod('_saveAsFinished');
        $method->setAccessible(true);
        $method->invokeArgs($this->_exportModel, array());
    }
}
