<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Catalog\Layer\Filter;

/**
 * Suppress coupling warning, because it is rather issue of the class tested, than the test itself
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_store;

    /**
     * @var \Magento\Catalog\Model\Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layer;

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\PriceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Price|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceFilterItem;

    /**
     * @var \Magento\Search\Model\Resource\Engine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceEngine;

    /**
     * @var \Magento\App\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cache;

    /**
     * @var \Magento\Search\Model\Resource\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productCollection;

    /**
     * @var \Magento\Search\Model\Layer\Category\Filter\Price
     */
    protected $_model;

    public function setUp()
    {
        $this->_store = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManager = $this->getMock('\Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);
        $this->_storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_productCollection = $this->getMock('\Magento\Search\Model\Resource\Collection', array(), array(), '',
            false);
        $this->_layer = $this->getMock('\Magento\Catalog\Model\Layer\Category', array(), array(), '', false);
        $this->_layer->expects($this->any())
            ->method('getProductCollection')
            ->will($this->returnValue($this->_productCollection));

        $this->_priceFactory = $this->getMock('\Magento\Catalog\Model\Resource\Layer\Filter\PriceFactory',
            array('create'), array(), '', false);
        $this->_priceFilterItem = $this->getMock('\Magento\Catalog\Model\Resource\Layer\Filter\Price', array(),
            array(), '', false);
        $this->_priceFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_priceFilterItem));

        $this->_resourceEngine = $this->getMock('\Magento\Search\Model\Resource\Engine', array(), array(), '', false);

        $this->_cache = $this->getMock('\Magento\App\CacheInterface', array(), array(), '', false);


        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManager->getObject(
            '\Magento\Search\Model\Layer\Category\Filter\Price',
            array(
                'storeManager' => $this->_storeManager,
                'layer' => $this->_layer,
                'filterPriceFactory' => $this->_priceFactory,
                'resourceEngine' => $this->_resourceEngine,
                'cache' => $this->_cache,
            )
        );
    }

    public function testGetMaxPriceIntCached()
    {
        $this->_productCollection->expects(
            $this->once()
        )->method(
            'getExtendedSearchParams'
        )->will(
            $this->returnValue(array('param1' => 'value1'))
        );

        $this->_cache->expects($this->once())->method('load')->will($this->returnValue(143));

        $this->_model->setData('currency_rate', 1);
        $result = $this->_model->getMaxPriceInt();
        $this->assertEquals(143, $result);
    }

    public function testAddFacetConditionImprovedAndCached()
    {
        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with(\Magento\Search\Model\Layer\Category\Filter\Price::XML_PATH_RANGE_CALCULATION)
            ->will($this->returnValue(\Magento\Search\Model\Layer\Category\Filter\Price::RANGE_CALCULATION_IMPROVED));

        $separators = '*-9,9-19';
        $this->_cache->expects($this->once())->method('load')->will($this->returnValue($separators));

        $this->_resourceEngine->expects(
            $this->once()
        )->method(
            'getSearchEngineFieldName'
        )->with(
            'price'
        )->will(
            $this->returnValue('price_field')
        );

        $expectedFacets = array(array('from' => '*', 'to' => 8.999), array('from' => 8.999, 'to' => 18.999));
        $this->_productCollection->expects(
            $this->at(1)
        )->method(
            'setFacetCondition'
        )->with(
            'price_field',
            $expectedFacets
        );
        $this->_productCollection->expects(
            $this->at(0)
        )->method(
            'getExtendedSearchParams'
        )->will(
            $this->returnValue(array('param1' => 'value1'))
        );

        $this->_model->setData('currency_rate', 1);
        $this->_model->addFacetCondition();
    }
}
