<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_ObserverTest extends PHPUnit_Framework_TestCase
{

    protected $_unitPriceAttributes = array(
        'unit_price_amount', 'unit_price_unit', 'unit_price_base_amount', 'unit_price_base_unit'
    );

    protected function createProduct()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        return $helper->getObject('Magento_Catalog_Model_Product');
    }

    public function testBcpUpdateDefaultsOnConfigurableProduct()
    {
        $varienObserver = new Magento_Event_Observer();
        $simpleProduct = $this->createProduct();
        $product = $this->createProduct();
        $event = new Magento_Event();

        $event->setData('product', $product);
        $event->setData('simple_product', $simpleProduct);

        $varienObserver->setEvent($event);

        foreach ($this->_unitPriceAttributes as $attributeCode) {
            $simpleProduct->setDataUsingMethod($attributeCode, '1');
        }

        $testObj = new Saas_UnitPrice_Model_Observer();
        $testObj->bcpUpdateDefaultsOnConfigurableProduct($varienObserver);

        foreach ($this->_unitPriceAttributes as $attributeCode) {
            $this->assertEquals(
                $simpleProduct->getDataUsingMethod($attributeCode), $product->getDataUsingMethod($attributeCode)
            );
        }
    }

    public function testCatalogProductLoadAfter()
    {
        $varienObserver = new Magento_Event_Observer();
        $product = $this->createProduct();
        $varienObserver->setProduct($product);

        $frontend = $this->getMock('Mage_Eav_Model_Entity_Attribute_Frontend_Default');
        $frontend->expects($this->any())
            ->method('getValue')
            ->with($product)
            ->will($this->returnValue('1'));

        $attribute = $this->getMockBuilder('Mage_Eav_Model_Entity_Attribute')
            ->setMethods(array('getFrontend', 'loadByCode'))
            ->disableOriginalConstructor()
            ->getMock();
        $attribute->expects($this->any())
            ->method('getFrontend')
            ->will($this->returnValue($frontend));

        $helper = $this->getMock('Saas_UnitPrice_Helper_Data', array(), array(), '', false);
        $helper->expects($this->any())
            ->method('moduleActive')
            ->will($this->returnValue(true));

        $observer = $this->getMockBuilder('Saas_UnitPrice_Model_Observer')
            ->setMethods(array('_getSaasUnitPriceHelperData', '_getEavEntityAttributeModel'))
            ->disableOriginalConstructor()
            ->getMock();
        $observer->expects($this->any())
            ->method('_getSaasUnitPriceHelperData')
            ->will($this->returnValue($helper));

        $attributes = array();

        foreach ($this->_unitPriceAttributes as $key => $attributeCode) {
            $attributes[$key] = $this->getMockBuilder('Mage_Eav_Model_Entity_Attribute')
                ->setMockClassName('Mage_Eav_Model_Entity_Attribute' . $attributeCode)
                ->setMethods(array('getFrontend', 'loadByCode'))
                ->disableOriginalConstructor()
                ->getMock();
            $attributes[$key]->expects($this->any())
                ->method('getFrontend')
                ->will($this->returnValue($frontend));
            $attributes[$key]->expects($this->once())
                ->method('loadByCode')
                ->with('catalog_product', $attributeCode)
                ->will($this->returnSelf());

            $observer->expects($this->at($key + 1))
                ->method('_getEavEntityAttributeModel')
                ->will($this->returnValue($attributes[$key]));
        }

        $observer->catalogProductLoadAfter($varienObserver);

        foreach ($this->_unitPriceAttributes as $attributeCode) {
            $product = $varienObserver->getProduct();
            $this->assertNotNull($product->getDataUsingMethod($attributeCode));
        }
    }
}
