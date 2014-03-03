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

namespace Magento\Catalog\Model;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_priceProcessorMock;

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexIndexerMock;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProductHelperMock;

    public function setUp()
    {
        $this->_priceProcessorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor', array(), array(), '', false
        );

        $this->_indexIndexerMock = $this->getMock(
            'Magento\Index\Model\Indexer', array(), array(), '', false
        );

        $this->_catalogProductHelperMock = $this->getMock(
            'Magento\Catalog\Helper\Product', array(), array(), '', false
        );

        $stateMock = $this->getMock(
            'Magento\App\State', array('getAreaCode'), array(), '', false
        );

        $stateMock->expects($this->any())
            ->method('getAreaCode')
            ->will($this->returnValue(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE));

        $eventManagerMock = $this->getMock(
            'Magento\Event\ManagerInterface', array(), array(), '', false
        );

        $cacheInterfaceMock = $this->getMock(
            'Magento\App\CacheInterface', array(), array(), '', false
        );

        $contextMock = $this->getMock(
            '\Magento\Core\Model\Context',
            array('getEventDispatcher', 'getCacheManager', 'getAppState'), array(), '', false
        );

        $contextMock->expects($this->any())
            ->method('getAppState')
            ->will($this->returnValue($stateMock));

        $contextMock->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($eventManagerMock));

        $contextMock->expects($this->any())
            ->method('getCacheManager')
            ->will($this->returnValue($cacheInterfaceMock));

        $arguments = array(
            'context' => $contextMock,
            'productPriceIndexerProcessor' => $this->_priceProcessorMock,
            'indexIndexer' => $this->_indexIndexerMock,
            'catalogProduct' => $this->_catalogProductHelperMock
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManager->getObject('Magento\Catalog\Model\Product', $arguments);
    }

    public function testPriceIndexerAfterDeleteCommitProduct()
    {
        $this->_priceProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with($this->equalTo(1));

        $this->_indexIndexerMock->expects($this->once())
            ->method('indexEvents')
            ->with(\Magento\Catalog\Model\Product::ENTITY, \Magento\Index\Model\Event::TYPE_DELETE);

        $this->_model->setId(1);
        $this->_model->delete();
    }
    /**
     *
     * @param boolean $isNewObject
     * @param boolean $isDataForPriceIndexerWasChanged
     * @dataProvider getData
     */
    public function testReindexCallback($isNewObject, $isDataForPriceIndexerWasChanged)
    {
        if ($isNewObject) {
            $this->_model->setId(null);
        } else {
            $this->_model->setId(1);
        }

        $this->_catalogProductHelperMock->expects($this->any())
            ->method('isDataForPriceIndexerWasChanged')
            ->with($this->equalTo($this->_model))
            ->will($this->returnValue($isDataForPriceIndexerWasChanged));

        if ($isNewObject || $isDataForPriceIndexerWasChanged) {
            $this->_priceProcessorMock->expects($this->once())
                ->method('reindexRow');
        } else {
            $this->_priceProcessorMock->expects($this->never())
                ->method('reindexRow');
        }

        $this->assertEquals($this->_model, $this->_model->reindexCallback());
    }

    /**
     * Data provider for testReindexCallback
     * @return array
     */
    public function getData()
    {
        return array(
            array(false, true),
            array(true, false),
            array(true, true),
            array(false, false),
        );
    }
}
