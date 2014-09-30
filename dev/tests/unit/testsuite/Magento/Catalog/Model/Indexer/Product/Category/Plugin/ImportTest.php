<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Category\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterImportSource()
    {
        $productCategoryProcessorMock = $this->getMockBuilder('Magento\Catalog\Model\Indexer\Product\Category\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $productCategoryProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');


        $subjectMock = $this->getMockBuilder('Magento\ImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $import = new \stdClass();

        $model = new \Magento\CatalogImportExport\Model\Indexer\Product\Category\Plugin\Import($productCategoryProcessorMock);

        $this->assertEquals(
            $import,
            $model->afterImportSource($subjectMock, $import)
        );
    }
}
