<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Plugin;

class ImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Plugin\Import
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    public function setUp()
    {
        $this->_indexerMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Indexer\Stock\Processor',
            array(),
            array(),
            '',
            false
        );
        $this->_model = new \Magento\CatalogInventory\Model\Indexer\Stock\Plugin\Import($this->_indexerMock);
    }

    public function testAfterImportSource()
    {
        $subjectMock = $this->getMock('Magento\ImportExport\Model\Import', array(), array(), '', false);
        $result = 'result';

        $this->_indexerMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->assertEquals(
            $result,
            $this->_model->afterImportSource($subjectMock, $result)
        );
    }
}
