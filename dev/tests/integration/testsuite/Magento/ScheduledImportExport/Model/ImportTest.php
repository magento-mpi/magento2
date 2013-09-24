<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ScheduledImportExport\Model;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRunSchedule()
    {
        /** @var Magento_TestFramework_ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $productModel = $objectManager->create('Magento\Catalog\Model\Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        $importExportData = $objectManager->get('Magento\ImportExport\Helper\Data');
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        
        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        /** @var \Magento\ImportExport\Model\Import $model */
        $model = $this->getMock(
            'Magento\ScheduledImportExport\Model\Import',
            array('reindexAll'),
            array(
                'logger' => $logger,
                'importExportData' => $importExportData,
                'coreConfig' => $objectManager->create('Magento\Core\Model\Config'),
                'config' => $objectManager->create('Magento\ImportExport\Model\Config'),
                'data' => array('entity' => 'catalog_product', 'behavior' => 'append')
        ));
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnSelf());

        $operation = $objectManager->create('Magento\ScheduledImportExport\Model\Scheduled\Operation');
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
