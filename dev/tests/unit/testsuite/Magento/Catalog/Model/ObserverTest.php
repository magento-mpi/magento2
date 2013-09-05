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
     * @var Magento_Catalog_Model_Observer
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_model = new Magento_Catalog_Model_Observer();
        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
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
