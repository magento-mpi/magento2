<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogImportExport\Model\Indexer\Product\Price\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\CatalogImportExport\Model\Indexer\Product\Price\Plugin\Import
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    public function setUp()
    {
        $this->markTestIncomplete('MAGETWO-28043');
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'invalidate'),
            array(),
            '',
            false
        );
        $this->_indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));

        $this->_model = $this->_objectManager->getObject(
            'Magento\CatalogImportExport\Model\Indexer\Product\Price\Plugin\Import',
            array('indexer' => $this->_indexerMock)
        );
    }

    public function testAfterImportSource()
    {
        $this->_indexerMock->expects($this->once())->method('invalidate');

        $importMock = $this->getMock('Magento\ImportExport\Model\Import', array(), array(), '', false);
        $this->assertEquals('return_value', $this->_model->afterImportSource($importMock, 'return_value'));
    }
}
