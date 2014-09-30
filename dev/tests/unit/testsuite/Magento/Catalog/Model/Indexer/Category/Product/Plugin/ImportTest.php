<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Product\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterImportSource()
    {
        $categoryProductProcessorMock = $this->getMockBuilder('Magento\Catalog\Model\Indexer\Category\Product\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $categoryProductProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');


        $subjectMock = $this->getMockBuilder('Magento\ImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $import = new \stdClass();

        $model = new \Magento\CatalogImportExport\Model\Indexer\Category\Product\Plugin\Import($categoryProductProcessorMock);

        $this->assertEquals(
            $import,
            $model->afterImportSource($subjectMock, $import)
        );
    }
}
