<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ScheduledImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRunSchedule()
    {
        /** @var Magento_TestFramework_ObjectManager $objectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $productModel = $objectManager->create('Magento_Catalog_Model_Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock('Magento_ScheduledImportExport_Model_Import', array('reindexAll'), array(
            $objectManager->get('Magento_Core_Model_Logger'),
            $objectManager->get('Magento_Core_Model_Dir'),
            $objectManager->get('Magento_Core_Model_Log_AdapterFactory'),
            $objectManager->get('Magento_ImportExport_Helper_Data'),
            $objectManager->get('Magento_Core_Model_Config'),
            $objectManager->get('Magento_ImportExport_Model_Import_ConfigInterface'),
            $objectManager->get('Magento_ImportExport_Model_Import_Entity_Factory'),
            $objectManager->get('Magento_ImportExport_Model_Resource_Import_Data'),
            $objectManager->get('Magento_ImportExport_Model_Export_Adapter_CsvFactory'),
            $objectManager->get('Zend_File_Transfer_Adapter_HttpFactory'),
            $objectManager->get('Magento_Core_Model_File_UploaderFactory'),
            $objectManager->get('Magento_ImportExport_Model_Source_Import_Behavior_Factory'),
            $objectManager->get('Magento_Index_Model_Indexer'),
            array(
                'entity'   => 'catalog_product',
                'behavior' => 'append',
            )
        ));
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnSelf());

        $operation = $objectManager->create('Magento_ScheduledImportExport_Model_Scheduled_Operation');
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
