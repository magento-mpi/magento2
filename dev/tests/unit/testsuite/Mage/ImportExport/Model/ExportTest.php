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
     * Test get file name with adapter file name
     */
    public function testGetFileNameWithAdapterFileName()
    {
        $model = new Stub_UnitTest_Mage_ImportExport_Model_Export();
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
        $model = new Stub_UnitTest_Mage_ImportExport_Model_Export();
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

/**
 * We need this class to rewrite method _getEntityAdapter
 *
 * @method boolean setEntity() setEntity(string)
 */
class Stub_UnitTest_Mage_ImportExport_Model_Export extends Mage_ImportExport_Model_Export
{
    /**
     * Create instance of entity adapter and returns it.
     *
     * @return Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_Test
     */
    public function getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $this->_entityAdapter = new Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_Test();
        }
        return $this->_entityAdapter;
    }

    /**
     * Get writer object.
     *
     * @return Stub_UnitTest_Mage_ImportExport_Model_Export_Adapter_Test
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $this->_writer = new Stub_UnitTest_Mage_ImportExport_Model_Export_Adapter_Test();
        }
        return $this->_writer;
    }
}

/**
 * Stub for entity adapter
 */
class Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_Test
    extends Mage_ImportExport_Model_Export_Entity_V2_Abstract
{
    /**
     * Disable parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Export process.
     */
    public function export()
    {
    }

    /**
     * Entity attributes collection getter.
     */
    public function getAttributeCollection()
    {
    }

    /**
     * EAV entity type code getter.
     */
    public function getEntityTypeCode()
    {
    }
}

/**
 * Stub for export adapter
 */
class Stub_UnitTest_Mage_ImportExport_Model_Export_Adapter_Test
    extends Mage_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Write one row
     *
     * @param array $rowData
     */
    public function writeRow(array $rowData)
    {
        return $rowData;
    }

    /**
     * Get file extension
     *
     * @return string
     */
    public function getFileExtension()
    {
        return 'csv';
    }
}
