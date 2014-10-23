<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

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
        $product = $productModel->loadByAttribute('sku', 'product_100500');
        // fixture
        $this->assertFalse($product);

        $model = $objectManager->create(
            'Magento\ScheduledImportExport\Model\Import',
            array(
                'data' => array(
                    'entity' => 'catalog_product',
                    'behavior' => 'append',
                ),
            )
        );

        $directoryList = $objectManager->create(
            'Magento\Framework\App\Filesystem\DirectoryList',
            array(
                'config' => array(
                    DirectoryList::VAR_DIR => array(DirectoryList::PATH => __DIR__ . '/../_files/')
                ),
                'root' => BP
            )
        );
        $filesystem = $objectManager->create(
            'Magento\Framework\Filesystem',
            array('directoryList' => $directoryList)
        );
        $operation = $objectManager->create(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            array('filesystem' => $filesystem)
        );
        $operation->setFileInfo(
            [
                'file_name' => 'product.csv',
                'server_type' => 'file',
                'file_path' => '/../_files'
            ]
        );
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
