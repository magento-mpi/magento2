<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Model_Import_EntityAbstract
 */
class Magento_ImportExport_Model_Import_EntityAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for method _saveValidatedBunches()
     */
    public function testSaveValidatedBunches()
    {
        $source = new Magento_ImportExport_Model_Import_Source_Csv(
            __DIR__ . '/Entity/Eav/_files/customers_for_validation_test.csv'
        );
        $source->rewind();
        $expected = $source->current();

        $storeConfig = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Store_Config');
        /** @var $model Magento_ImportExport_Model_Import_EntityAbstract|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass(
            'Magento_ImportExport_Model_Import_EntityAbstract', array($storeConfig)
        );
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

        $dataSourceModel = Magento_ImportExport_Model_Import::getDataSourceModel();
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}
