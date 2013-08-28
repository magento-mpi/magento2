<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_ImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRunSchedule()
    {
        $productModel = Mage::getModel('Magento_Catalog_Model_Product');
        $product = $productModel->loadByAttribute('sku', 'product_100500'); // fixture
        $this->assertFalse($product);

        // Mock the reindexAll() method, because it has DDL operations, thus breaks DB-isolating transaction
        $model = $this->getMock(
            'Enterprise_ImportExport_Model_Import',
            array('reindexAll'),
            array(array('entity' => 'catalog_product', 'behavior' => 'append'))
        );
        $model->expects($this->once())
            ->method('reindexAll')
            ->will($this->returnValue($model));

        $operation = Mage::getModel('Enterprise_ImportExport_Model_Scheduled_Operation');
        $operation->setFileInfo(array(
            'file_name' => __DIR__ . '/../_files/product.csv',
            'server_type' => 'file',
        ));
        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }
}
