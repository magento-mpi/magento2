<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_ExportFinishedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Model_Export
     */
    protected $_exportModel;

    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_exportModel = $objectManager->getObject('Saas_ImportExport_Model_Export', array());
    }

    public function testGetIsFinished()
    {
        $this->assertFalse($this->_exportModel->isFinished());
        $this->_callFinishExportMethod();
        $this->assertTrue($this->_exportModel->isFinished());
    }

    protected function _callFinishExportMethod()
    {
        $class = new ReflectionClass('Saas_ImportExport_Model_Export');
        $method = $class->getMethod('_saveAsFinished');
        $method->setAccessible(true);
        $method->invokeArgs($this->_exportModel, array());
    }
}
