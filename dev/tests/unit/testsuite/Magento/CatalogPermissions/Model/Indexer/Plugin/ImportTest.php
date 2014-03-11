<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

use Magento\CatalogPermissions\Model\Indexer\Category;
use Magento\CatalogPermissions\Model\Indexer\Product;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Covered import plugun
     *
     * @test
     */
    public function testAfterImportSource()
    {
        $config = $this->getMockBuilder('Magento\CatalogPermissions\App\ConfigInterface')->getMock();

        $indexer = $this->getMockBuilder('Magento\Indexer\Model\Indexer')
            ->disableOriginalConstructor()
            ->getMock();

        $indexerFactory = $this->getMockBuilder('Magento\Indexer\Model\IndexerFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $config->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $indexerFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($indexer));

        $indexer->expects($this->exactly(2))
            ->method('load')
            ->with($this->logicalOr(Category::INDEXER_ID, Product::INDEXER_ID))
            ->will($this->returnSelf());

        $indexer->expects($this->exactly(2))
            ->method('invalidate');

        $import = new Import($config, $indexerFactory);

        $subject = $this->getMockBuilder('Magento\ImportExport\Model\Import')->disableOriginalConstructor()->getMock();

        $this->assertEquals('import', $import->afterImportSource($subject, 'import'));
    }
}
