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

/**
 * Class Magento_Catalog_Model_ObserverTest
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_objectHelper;

    /**
     * @var Magento_Event_Observer
     */
    protected $_observer;

    /**
     * @var Magento_Catalog_Model_Observer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_catalogCategory = $this->getMock('Magento_Catalog_Helper_Category', array(), array(), '', false);
        $this->_catalogData = $this->getMock('Magento_Catalog_Helper_Data', array(), array(), '', false);
        $urlFactoryMock = $this->getMock('Magento_Catalog_Model_UrlFactory', array(), array(), '', false);
        $categoryFlatFactoryMock = $this->getMock('Magento_Catalog_Model_Resource_Category_FlatFactory', array(),
            array(), '', false);
        $resourceProductFactoryMock = $this->getMock('Magento_Catalog_Model_Resource_ProductFactory', array(),
            array(), '', false);
        $this->_catalogCategoryFlat = $this->getMock(
            'Magento_Catalog_Helper_Category_Flat', array(), array(), '', false
        );
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_model = $this->_objectHelper->getObject('Magento_Catalog_Model_Observer', array(
            'catalogCategory' => $this->_catalogCategory,
            'catalogData' => $this->_catalogData,
            'catalogCategoryFlat' => $this->_catalogCategoryFlat,
            'coreConfig' => $coreConfig,
            'urlFactory' => $urlFactoryMock,
            'flatResourceFactory' => $categoryFlatFactoryMock,
            'productResourceFactory' => $resourceProductFactoryMock,
        ));
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
    }

    public function testTransitionProductTypeSimple()
    {
        $product = new Magento_Object(array('type_id' => 'simple'));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtual()
    {
        $product = new Magento_Object(array('type_id' => 'virtual', 'is_virtual' => ''));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeSimpleToVirtual()
    {
        $product = new Magento_Object(array('type_id' => 'simple', 'is_virtual' => ''));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtualToSimple()
    {
        $product = new Magento_Object(array('type_id' => 'virtual'));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeConfigurableToSimple()
    {
        $product = new Magento_Object(array('type_id' => 'configurable'));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeConfigurableToVirtual()
    {
        $product = new Magento_Object(array('type_id' => 'configurable', 'is_virtual' => '1'));
        $this->_observer = new Magento_Event_Observer(array('product' => $product, 'request' => $this->_requestMock));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }
}
