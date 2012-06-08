<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Export_Entity_V2_Abstract.
 */
class Mage_ImportExport_Model_Export_Entity_V2_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testGetFileNameAndSetFileName()
    {
        $model = new Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_TestSetAndGet();
        $testFileName = 'test_file_name';

        $fileName = $model->getFileName();
        $this->assertNull($fileName);

        $model->setFileName($testFileName);
        $this->assertEquals($testFileName, $model->getFileName());

        $fileName = $model->getFileName();
        $this->assertEquals($testFileName, $fileName);
    }
}

/**
 * Stub for entity adapter
 */
class Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_TestSetAndGet
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
