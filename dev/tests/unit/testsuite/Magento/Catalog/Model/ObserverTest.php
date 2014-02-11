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

/**
 * Class \Magento\Catalog\Model\ObserverTest
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Catalog\Model\Observer
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $catalogCategory = $this->getMock('Magento\Catalog\Helper\Category', array(), array(), '', false);
        $catalogData = $this->getMock('Magento\Catalog\Helper\Data', array(), array(), '', false);
        $urlFactoryMock = $this->getMock('Magento\Catalog\Model\UrlFactory', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento\Catalog\Model\Resource\ProductFactory', array(),
            array(), '', false);
        $categoryFlatState = $this->getMock('Magento\Catalog\Model\Indexer\Category\Flat\State', array(),
            array(), '', false);
        $coreConfig = $this->getMock('Magento\App\ReinitableConfigInterface', array(), array(), '', false);
        $this->_model = $this->_objectHelper->getObject('Magento\Catalog\Model\Observer', array(
            'catalogCategory' => $catalogCategory,
            'catalogData' => $catalogData,
            'coreConfig' => $coreConfig,
            'urlFactory' => $urlFactoryMock,
            'productResourceFactory' => $productFactoryMock,
            'categoryFlatState' => $categoryFlatState,
        ));
        $this->_requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
    }

    public function testTransitionProductTypeSimple()
    {
        $product = new \Magento\Object(array('type_id' => 'simple'));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtual()
    {
        $product = new \Magento\Object(array('type_id' => 'virtual', 'is_virtual' => ''));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeSimpleToVirtual()
    {
        $product = new \Magento\Object(array('type_id' => 'simple', 'is_virtual' => ''));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtualToSimple()
    {
        $product = new \Magento\Object(array('type_id' => 'virtual'));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeConfigurableToSimple()
    {
        $product = new \Magento\Object(array('type_id' => 'configurable'));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeConfigurableToVirtual()
    {
        $product = new \Magento\Object(array('type_id' => 'configurable', 'is_virtual' => '1'));
        $this->_observer = new \Magento\Event\Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }
}
