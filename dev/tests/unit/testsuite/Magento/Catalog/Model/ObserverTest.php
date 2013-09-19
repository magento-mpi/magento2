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

class Magento_Catalog_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Catalog\Model\Observer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_catalogCategory = $this->getMock('Magento\Catalog\Helper\Category', array(), array(), '', false);
        $this->_catalogData = $this->getMock('Magento\Catalog\Helper\Data', array(), array(), '', false);
        $this->_catalogCategoryFlat = $this->getMock(
            'Magento\Catalog\Helper\Category\Flat', array(), array(), '', false
        );
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);
        $this->_model = new \Magento\Catalog\Model\Observer(
            $this->_catalogCategory,
            $this->_catalogData,
            $this->_catalogCategoryFlat,
            $coreConfig
        );
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
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
