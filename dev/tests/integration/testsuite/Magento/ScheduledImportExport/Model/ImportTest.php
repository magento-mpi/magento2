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
            $objectManager->get('Magento\Core\Model\Logger'),
            $objectManager->get('Magento\Core\Model\Dir'),
            $objectManager->get('Magento\Core\Model\Log\AdapterFactory'),
            $objectManager->get('Magento\ImportExport\Helper\Data'),
            $objectManager->get('Magento\Core\Model\Config'),
            $objectManager->get('Magento\ImportExport\Model\Import\ConfigInterface'),
            $objectManager->get('Magento\ImportExport\Model\Import\Entity\Factory'),
            $objectManager->get('Magento\ImportExport\Model\Resource\Import\Data'),
            $objectManager->get('Magento\ImportExport\Model\Export\Adapter\CsvFactory'),
            $objectManager->get('Zend_File_Transfer_Adapter_HttpFactory'),
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
