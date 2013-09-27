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

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $model Magento_ImportExport_Model_Import_EntityAbstract|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass('Magento_ImportExport_Model_Import_EntityAbstract', array(
            $objectManager->get('Magento_Core_Helper_Data'),
            $objectManager->get('Magento_Core_Helper_String'),
            $objectManager->get('Magento_Core_Model_Store_Config'),
            $objectManager->get('Magento_ImportExport_Model_ImportFactory'),
            $objectManager->get('Magento_ImportExport_Model_Resource_Helper_Mysql4'),
            $objectManager->get('Magento_Core_Model_Resource'),
        ));
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

        /** @var $dataSourceModel Magento_ImportExport_Model_Resource_Import_Data */
        $dataSourceModel = $objectManager->get('Magento_ImportExport_Model_Resource_Import_Data');
        $this->assertCount(1, $dataSourceModel->getIterator());

        $bunch = $dataSourceModel->getNextBunch();
        $this->assertEquals($expected, $bunch[0]);

        //Delete created bunch from DB
        $dataSourceModel->cleanBunches();
    }
}
