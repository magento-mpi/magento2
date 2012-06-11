<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Export
 */
class Mage_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Return mock for Mage_ImportExport_Model_Export class
     *
     * @return Mage_ImportExport_Model_Export
     */
    protected function _getMageImportExportModelExportStub()
    {
        /** @var $stubModelExportEntityV2Abstract Mage_ImportExport_Model_Export_Entity_V2_Abstract */
        $stubEntityAbstract = $this->getMockForAbstractClass(
            'Mage_ImportExport_Model_Export_Entity_V2_Abstract',
            array(),
            '',
            false
        );

        /** @var $stubAdapterTest Mage_ImportExport_Model_Export_Adapter_Abstract */
        $stubAdapterTest = $this->getMockForAbstractClass(
            'Mage_ImportExport_Model_Export_Adapter_Abstract',
            array(),
            '',
            true,
            true,
            true,
            array('getFileExtension')
        );
        $stubAdapterTest->expects($this->any())
            ->method('getFileExtension')
            ->will($this->returnValue('csv'));

        /** @var $stubModelExport Mage_ImportExport_Model_Export */
        $stubModelExport = $this->getMock(
            'Mage_ImportExport_Model_Export',
            array('getEntityAdapter', '_getEntityAdapter', '_getWriter')
        );
        $stubModelExport->expects($this->any())
            ->method('getEntityAdapter')
            ->will($this->returnValue($stubEntityAbstract));
        $stubModelExport->expects($this->any())
            ->method('_getEntityAdapter')
            ->will($this->returnValue($stubEntityAbstract));
        $stubModelExport->expects($this->any())
            ->method('_getWriter')
            ->will($this->returnValue($stubAdapterTest));

        return $stubModelExport;
    }

    /**
     * Test get file name with adapter file name
     */
    public function testGetFileNameWithAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportStub();
        $model->getEntityAdapter()->setFileName('test_file_name');

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = 'test_file_name_' . $correctDateTime . '.csv';
        $this->assertEquals($correctFileName, $fileName);
    }

    /**
     * Test get file name without adapter file name
     */
    public function testGetFileNameWithoutAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportStub();
        $model->getEntityAdapter()->setFileName(null);
        $model->setEntity('test_entity');

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = 'test_entity_' . $correctDateTime . '.csv';
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
