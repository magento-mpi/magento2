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
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $productModel = $objectManager->create('Magento\Catalog\Model\Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        $importExportData = $objectManager->get('Magento\ImportExport\Helper\Data');
        $importConfig = $objectManager->get('Magento\ImportExport\Model\Import\ConfigInterface');
        $logger = $objectManager->get('Magento\Core\Model\Logger');
        $indexer = $objectManager->get('Magento\Index\Model\Indexer');

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock('Magento\ScheduledImportExport\Model\Import', array('reindexAll'), array(
            $indexer, $logger, $importExportData, $importConfig, array(
                'entity'   => 'catalog_product',
                'behavior' => 'append',
            )
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
