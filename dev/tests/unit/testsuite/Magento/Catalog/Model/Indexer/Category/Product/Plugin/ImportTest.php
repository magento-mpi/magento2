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
        $eavProcessorMock = $this->getMockBuilder('\Magento\Indexer\Model\IndexerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $eavProcessorMock->expects($this->once())
            ->method('load')
            ->with(\Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID);


        $subjectMock = $this->getMockBuilder('Magento\ImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $import = new \stdClass();

        $model = new \Magento\CatalogImportExport\Model\Indexer\Category\Product\Plugin\Import($eavProcessorMock);

        $this->assertEquals(
            $import,
            $model->afterImportSource($subjectMock, $import)
        );
    }
}
