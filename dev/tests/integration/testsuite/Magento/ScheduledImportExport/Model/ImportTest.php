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

        $importExportData = $objectManager->get('Magento_ImportExport_Helper_Data');
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        
        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        /** @var Magento_ImportExport_Model_Import $model */
        $model = $this->getMock(
            'Magento_ScheduledImportExport_Model_Import',
            array('reindexAll'),
            array(
                'logger' => $logger,
                'importExportData' => $importExportData,
                'coreConfig' => $objectManager->create('Magento_Core_Model_Config'),
                'config' => $objectManager->create('Magento_ImportExport_Model_Config'),
                'data' => array('entity' => 'catalog_product', 'behavior' => 'append')
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
