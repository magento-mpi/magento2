<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Event_Observer
     */
    protected $_observer;

    /**
     * @var Mage_Catalog_Model_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Observer();
    }

    public function testTransitionProductTypeSimple()
    {
        $this->markTestIncomplete('MAGETWO-4796');
        $product = new Varien_Object(array('type_id' => 'simple'));
        $this->_observer = new Varien_Event_Observer(array('product' => $product));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtual()
    {
        $this->markTestIncomplete('MAGETWO-4796');
        $product = new Varien_Object(array('type_id' => 'virtual', 'is_virtual' => ''));
        $this->_observer = new Varien_Event_Observer(array('product' => $product));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeSimpleToVirtual()
    {
        $this->markTestIncomplete('MAGETWO-4796');
        $product = new Varien_Object(array('type_id' => 'simple', 'is_virtual' => ''));
        $this->_observer = new Varien_Event_Observer(array('product' => $product));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('virtual', $product->getTypeId());
    }

    public function testTransitionProductTypeVirtualToSimple()
    {
        $this->markTestIncomplete('MAGETWO-4796');
        $product = new Varien_Object(array('type_id' => 'virtual'));
        $this->_observer = new Varien_Event_Observer(array('product' => $product));
        $this->_model->transitionProductType($this->_observer);
        $this->assertEquals('simple', $product->getTypeId());
    }
}
