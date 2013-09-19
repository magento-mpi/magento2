<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Model_Export
 */
class Magento_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Extension for export file
     *
     * @var string
     */
    protected $_exportFileExtension = 'csv';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_exportConfigMock;

    /**
     * Return mock for Magento_ImportExport_Model_Export class
     *
     * @return Magento_ImportExport_Model_Export
     */
    protected function _getMageImportExportModelExportMock()
    {
        $this->_exportConfigMock = $this->getMock('Magento_ImportExport_Model_Export_ConfigInterface');

        /** @var $mockEntityAbstract Magento_ImportExport_Model_Export_EntityAbstract */
        $mockEntityAbstract = $this->getMockForAbstractClass(
            'Magento_ImportExport_Model_Export_EntityAbstract',
            array(),
            '',
            false
        );

        /** @var $mockAdapterTest Magento_ImportExport_Model_Export_Adapter_Abstract */
        $mockAdapterTest = $this->getMockForAbstractClass(
            'Magento_ImportExport_Model_Export_Adapter_Abstract',
            array(),
            '',
            false,
            true,
            true,
            array('getFileExtension')
        );
        $mockAdapterTest->expects($this->any())
            ->method('getFileExtension')
            ->will($this->returnValue($this->_exportFileExtension));

        /** @var $mockModelExport Magento_ImportExport_Model_Export */
        $mockModelExport = $this->getMock(
            'Magento_ImportExport_Model_Export',
            array('getEntityAdapter', '_getEntityAdapter', '_getWriter'),
            array($this->_exportConfigMock, array())
        );
        $mockModelExport->expects($this->any())
            ->method('getEntityAdapter')
            ->will($this->returnValue($mockEntityAbstract));
        $mockModelExport->expects($this->any())
            ->method('_getEntityAdapter')
            ->will($this->returnValue($mockEntityAbstract));
        $mockModelExport->expects($this->any())
            ->method('_getWriter')
            ->will($this->returnValue($mockAdapterTest));

        return $mockModelExport;
    }

    /**
     * Test get file name with adapter file name
     */
    public function testGetFileNameWithAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportMock();
        $basicFileName = 'test_file_name';
        $model->getEntityAdapter()->setFileName($basicFileName);

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = $basicFileName . '_' . $correctDateTime . '.' . $this->_exportFileExtension;
        $this->assertEquals($correctFileName, $fileName);
    }

    /**
     * Test get file name without adapter file name
     */
    public function testGetFileNameWithoutAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportMock();
        $model->getEntityAdapter()->setFileName(null);
        $basicFileName = 'test_entity';
        $model->setEntity($basicFileName);

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = $basicFileName . '_' . $correctDateTime . '.' . $this->_exportFileExtension;
        $this->assertEquals($correctFileName, $fileName);
    }

    /**
     * Get correct file creation time
     *
     * @param string $fileName
     * @return string|null
     */
    protected function _getCorrectDateTime($fileName)
    {
        preg_match('/(\d{8}_\d{6})/', $fileName, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }
}
