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
 * Test class for Mage_ImportExport_Model_Export_Entity_V2_Abstract.
 */
class Mage_ImportExport_Model_Export_Entity_V2_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for setter and getter of file name property
     */
    public function testGetFileNameAndSetFileName()
    {
        /** @var $model Mage_ImportExport_Model_Export_Entity_V2_Abstract */
        $model = $this->getMockForAbstractClass(
            'Mage_ImportExport_Model_Export_Entity_V2_Abstract',
            array(),
            'Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_V2_TestSetAndGet',
            false
        );

        $testFileName = 'test_file_name';

        $fileName = $model->getFileName();
        $this->assertNull($fileName);

        $model->setFileName($testFileName);
        $this->assertEquals($testFileName, $model->getFileName());

        $fileName = $model->getFileName();
        $this->assertEquals($testFileName, $fileName);
    }
}
