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
        $this->_exportMock = $this->getMock('Saas_ImportExport_Model_Export', array('_initParams', '_finishExport',
            '_paginateCollection', '_isCanExport', '_saveHeaderColumns', '_export', '_saveExportState'),
            array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_exportModel = $objectManager->getObject('Saas_ImportExport_Model_Export', array());
  }

    public function testExportIsProcess()
    {
        $this->_exportMock->expects($this->once())->method('_initParams');
        $this->_exportMock->expects($this->once())->method('_paginateCollection');
        $this->_exportMock->expects($this->once())->method('_isCanExport')->will($this->returnValue(true));
        $this->_exportMock->expects($this->once())->method('_saveHeaderColumns');
        $this->_exportMock->expects($this->once())->method('_export');
        $this->_exportMock->expects($this->once())->method('_saveExportState');
        $this->_exportMock->export(array());
    }

    public function testExportIsNotProcess()
    {
        $this->_exportMock->expects($this->once())->method('_initParams');
        $this->_exportMock->expects($this->once())->method('_paginateCollection');
        $this->_exportMock->expects($this->once())->method('_isCanExport')->will($this->returnValue(false));
        $this->_exportMock->expects($this->once())->method('_finishExport');
        $this->_exportMock->export(array());
    }

    public function testGetIsFinished()
    {
        $this->assertFalse($this->_exportModel->getIsFinished());
        $this->_callFinishExportMethod();
        $this->assertTrue($this->_exportModel->getIsFinished());
    }

    protected function _callFinishExportMethod() {
        $class = new ReflectionClass('Saas_ImportExport_Model_Export');
        $method = $class->getMethod('_finishExport');
        $method->setAccessible(true);
        $method->invokeArgs($this->_exportModel, array());
    }
}
