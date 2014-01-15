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

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock('Magento\ScheduledImportExport\Model\Import', array('reindexAll'), array(
            $objectManager->get('Magento\Logger'),
            $objectManager->get('Magento\Filesystem'),
            $objectManager->get('Magento\Core\Model\Log\AdapterFactory'),
            $objectManager->get('Magento\ImportExport\Helper\Data'),
            $objectManager->get('Magento\App\ConfigInterface'),
            $objectManager->get('Magento\ImportExport\Model\Import\ConfigInterface'),
            $objectManager->get('Magento\ImportExport\Model\Import\Entity\Factory'),
            $objectManager->get('Magento\ImportExport\Model\Resource\Import\Data'),
            $objectManager->get('Magento\ImportExport\Model\Export\Adapter\CsvFactory'),
            $objectManager->get('Magento\HTTP\Adapter\FileTransferFactory'),
            $objectManager->get('Magento\Core\Model\File\UploaderFactory'),
            $objectManager->get('Magento\ImportExport\Model\Source\Import\Behavior\Factory'),
            $objectManager->get('Magento\Index\Model\Indexer'),
            array(
                'entity'   => 'catalog_product',
                'behavior' => 'append',
            )
        ));
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnSelf());

        $directoryList = $objectManager->create(
            'Magento\Filesystem\DirectoryList',
            array(
                'directories' => array(
                    \Magento\Filesystem::VAR_DIR => array('path' => __DIR__ . '/../_files/')
                ),
                'root' => BP
            )
        );
        $filesystem = $objectManager->create('Magento\Filesystem', array('directoryList' => $directoryList));
        $operation = $objectManager->create(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            array('filesystem' => $filesystem)
        );
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
