<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price;


class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Observer
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \Magento\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var \Magento\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dateTimeMock;

    /**
     * @var \Magento\Core\Model\LocaleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_localeMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eavConfigMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceProcessorMock;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_storeManagerMock = $this->getMock('Magento\Core\Model\StoreManagerInterface', [], [], '', false);
        $this->_resourceMock = $this->getMock('Magento\App\Resource', [], [], '', false);
        $this->_dateTimeMock = $this->getMock('Magento\Stdlib\DateTime', [], [], '', false);
        $this->_localeMock = $this->getMock('Magento\Core\Model\LocaleInterface', [], [], '', false);
        $this->_eavConfigMock = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->_priceProcessorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor', [], [], '', false
        );

        $this->_model = $this->_objectManager->getObject('\Magento\Catalog\Model\Indexer\Product\Price\Observer', array(
            'storeManager' => $this->_storeManagerMock,
            'resource' => $this->_resourceMock,
            'dateTime' => $this->_dateTimeMock,
            'locale' => $this->_localeMock,
            'eavConfig' => $this->_eavConfigMock,
            'processor' => $this->_priceProcessorMock
        ));
    }

    public function testRefreshSpecialPrices()
    {
        $idsToProcess = array(1, 2, 3);

        $selectMock = $this->getMock('Magento\DB\Select', array(), array(), '', false);
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('where')
            ->will($this->returnSelf());

        $connectionMock = $this->getMock('Magento\DB\Adapter\AdapterInterface', array(), array(), '', false);
        $connectionMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $connectionMock->expects($this->any())
            ->method('fetchCol')
            ->with($selectMock, array('entity_id'))
            ->will($this->returnValue($idsToProcess));

        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->with('write')
            ->will($this->returnValue($connectionMock));

        $storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_storeManagerMock->expects($this->once())
            ->method('getStores')
            ->with(true)
            ->will($this->returnValue(array($storeMock)));

        $this->_localeMock->expects($this->once())
            ->method('storeTimeStamp')
            ->with($storeMock)
            ->will($this->returnValue(32000));

        $indexerMock = $this->getMock('Magento\Indexer\Model\Indexer', array(), array(), '', false);
        $indexerMock->expects($this->exactly(2))
            ->method('reindexList');

        $this->_priceProcessorMock->expects($this->exactly(2))
            ->method('getIndexer')
            ->will($this->returnValue($indexerMock));

        $attributeMock = $this->getMockForAbstractClass(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array(), '', false, true, true, array('__wakeup', 'getAttributeId')
        );
        $attributeMock->expects($this->any())
            ->method('getAttributeId')
            ->will($this->returnValue(1));

        $this->_eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($attributeMock));

        $this->_model->refreshSpecialPrices();
    }

}
