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
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-28043');
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->configMock = $this->getMockBuilder('Magento\CatalogPermissions\App\ConfigInterface')->getMock();
        $this->subject = $this->getMockBuilder('Magento\ImportExport\Model\Import')
            ->disableOriginalConstructor()->getMock();;
    }

    public function testAfterImportSourceWhenCatalogPermissionsEnabled()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $indexer = $this->getMockBuilder('Magento\Indexer\Model\Indexer')->disableOriginalConstructor()->getMock();
        $indexer->expects($this->exactly(2))->method('load')
            ->with($this->logicalOr(Category::INDEXER_ID, Product::INDEXER_ID))
            ->will($this->returnSelf());
        $indexer->expects($this->exactly(2))->method('invalidate');

        $indexerFactory = $this->getMockBuilder('Magento\Indexer\Model\IndexerFactory')
            ->disableOriginalConstructor()->setMethods(array('create'))->getMock();
        $indexerFactory->expects($this->exactly(2))->method('create')->will($this->returnValue($indexer));

        $import = $this->objectManager->getObject(
            'Magento\CatalogPermissions\Model\Indexer\Plugin\Import',
            array('config' => $this->configMock, 'indexerFactory' => $indexerFactory)
        );
        $this->assertEquals('import', $import->afterImportSource($this->subject, 'import'));
    }

    public function testAfterImportSourceWhenCatalogPermissionsDisabled()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));

        $import = $this->objectManager->getObject(
            'Magento\CatalogPermissions\Model\Indexer\Plugin\Import',
            array('config' => $this->configMock)
        );
        $this->assertEquals('import', $import->afterImportSource($this->subject, 'import'));
    }
}
