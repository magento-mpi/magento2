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
        $productModel = Mage::getModel('Magento_Catalog_Model_Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        $objectManager = Mage::getObjectManager();
        $importExportData = $objectManager->get('Magento_ImportExport_Helper_Data');
        $importConfig = $objectManager->get('Magento_ImportExport_Model_Import_Config');

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock('Magento_ScheduledImportExport_Model_Import', array('reindexAll'), array(
            $importExportData, $importConfig, array(
                'entity'   => 'catalog_product',
                'behavior' => 'append',
            )
        ));
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnSelf());

        $operation = Mage::getModel('Magento_ScheduledImportExport_Model_Scheduled_Operation');
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
