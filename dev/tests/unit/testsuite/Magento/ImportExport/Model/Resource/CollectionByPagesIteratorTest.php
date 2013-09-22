<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Resource\CollectionByPagesIterator
 */
class Magento_ImportExport_Model_Resource_CollectionByPagesIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Resource\CollectionByPagesIterator
     */
    protected $_resourceModel;

    protected function setUp()
    {
        $this->_resourceModel = new \Magento\ImportExport\Model\Resource\CollectionByPagesIterator();
    }

    protected function tearDown()
    {
        unset($this->_resourceModel);
    }

    /**
     * @covers \Magento\ImportExport\Model\Resource\CollectionByPagesIterator::iterate
     */
    public function testIterate()
    {
        $pageSize  = 2;
        $pageCount = 3;

        /** @var $callbackMock PHPUnit_Framework_MockObject_MockObject */
        $callbackMock = $this->getMock('stdClass', array('callback'));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');

        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);

        $entityFactory = $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);

        /** @var $collectionMock \Magento\Data\Collection\Db|PHPUnit_Framework_MockObject_MockObject */
        $collectionMock = $this->getMock('Magento\Data\Collection\Db',
            array('clear', 'setPageSize', 'setCurPage', 'count', 'getLastPageNumber', 'getSelect'),
            array($logger, $fetchStrategy, $entityFactory)
        );

        $collectionMock->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($select));

        $collectionMock->expects($this->exactly($pageCount + 1))
            ->method('clear')
            ->will($this->returnSelf());

        $collectionMock->expects($this->exactly($pageCount))
            ->method('setPageSize')
            ->will($this->returnSelf());

        $collectionMock->expects($this->exactly($pageCount))
            ->method('setCurPage')
            ->will($this->returnSelf());

        $collectionMock->expects($this->exactly($pageCount))
            ->method('count')
            ->will($this->returnValue($pageSize));

        $collectionMock->expects($this->exactly($pageCount))
            ->method('getLastPageNumber')
            ->will($this->returnValue($pageCount));

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            for ($rowNumber = 1; $rowNumber <= $pageSize; $rowNumber++) {
                $itemId = ($pageNumber - 1)*$pageSize + $rowNumber;
                $item = new \Magento\Object(array('id' => $itemId));
                $collectionMock->addItem($item);

                $callbackMock->expects($this->at($itemId - 1))
                    ->method('callback')
                    ->with($item);
            }
        }

        $this->_resourceModel->iterate($collectionMock, $pageSize, array(array($callbackMock, 'callback')));
    }
}
