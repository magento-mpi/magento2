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
        $productModel = Mage::getModel('Magento\Catalog\Model\Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock(
            '\Magento\ScheduledImportExport\Model\Import',
            array('reindexAll'),
            array(array('entity' => 'catalog_product', 'behavior' => 'append'))
        );
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnValue($model));

        $operation = Mage::getModel('Magento\ScheduledImportExport\Model\Scheduled\Operation');
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
