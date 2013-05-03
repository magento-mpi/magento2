<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_EntityAbstract
 */
class Mage_ImportExport_Model_Import_EntityAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     */
    public function testSaveValidatedBunches()
    {
        $filePathParts = array(__DIR__, 'Entity', 'Eav', '_files', 'customers_for_validation_test*.csv');
        $filePattern = glob(implode(DS, $filePathParts));
        foreach ($filePattern as $file) {
            $sourceFile = $file;
            break;
        }
        $source = new Mage_ImportExport_Model_Import_Source_Csv($sourceFile);
        $source->rewind();
        $expected = $source->current();
        /** @var $model Mage_ImportExport_Model_Import_EntityAbstract|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Import_EntityAbstract');
        $model->expects($this->any())
            ->method('validateRow')
            ->will($this->returnValue(true));
        $model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue('customer'));

        $model->setSource($source);

        $method = new ReflectionMethod($model, '_saveValidatedBunches');
        $method->setAccessible(true);
        $method->invoke($model);

        $dataSourceModel = Mage_ImportExport_Model_Import::getDataSourceModel();
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}
