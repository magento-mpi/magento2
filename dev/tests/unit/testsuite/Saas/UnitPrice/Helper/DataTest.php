<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Community_UnitPrice_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param string $expected
     * @dataProvider providerGetUnitPriceLabel
     */
    public function testGetUnitPriceLabel(array $data, $expected)
    {
        $product = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->setMethods(array('addCustomOption'))
            ->disableOriginalConstructor()
            ->getMock();
        $product->setUnitPriceAmount($data['unit_price_amount']);
        $product->setUnitPriceBaseAmount($data['unit_price_base_amount']);
        $product->setUnitPriceUnit($data['unit_price_unit']);
        $product->setUnitPriceBaseUnit($data['unit_price_base_unit']);
        $product->setFinalPrice($data['final_price']);

        $params = array('params' => array(
            'reference_unit' => $data['unit_price_base_unit'],
            'reference_amount' => $data['unit_price_base_amount']
        ));
        $basePriceModel = $this->getMockBuilder('Saas_UnitPrice_Model_Unitprice')
            ->setMethods(array('getUnitPrice'))
            ->getMock();

        $basePriceModel->expects($this->any())
            ->method('__construct')
            ->with($this->equalTo($params));
        $basePriceModel->expects($this->any())
            ->method('getUnitPrice')
            ->with(
                $this->equalTo($product->getUnitPriceAmount()),
                $this->equalTo($product->getUnitPriceUnit()),
                $this->equalTo($data['unit_price_incl_tax'])
            )->will($this->returnValue($data['base_price']));

        $map = array(
            array(
                'frontend_label',
                "{{unitprice}} / {{reference_amount}} {{reference_unit}} {{reference_unit_short}} / "
                    . "{{product_amount}} {{product_unit}} {{product_unit_short}}"
            ),
            array('unit_price_incl_tax', $data['unit_price_incl_tax'])
        );

        $methods = array('getModel', 'getHelperModel', '_loadDefaultUnitPriceValues', 'getConfig', '__', 'currency');
        $helper = $this->getMockBuilder('Saas_UnitPrice_Helper_Data')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();

        $helper->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValueMap($map));
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        $helper->expects($this->any())
            ->method('_loadDefaultUnitPriceValues')
            ->with($this->equalTo($product))
            ->will($this->returnValue($product));
        $helper->expects($this->any())
            ->method('getModel')
            ->with($this->equalTo('Saas_UnitPrice_Model_Unitprice'), $this->equalTo($params))
            ->will($this->returnValue($basePriceModel));
        $helper->expects($this->any())
            ->method('currency')
            ->with($this->equalTo($data['base_price']))
            ->will($this->returnArgument(0));

        $taxHelper = $this->getMockBuilder('Mage_Tax_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getPrice'))
            ->getMock();
        $taxHelper->expects($this->any())
            ->method('getPrice')
            ->with(
                $this->equalTo($product),
                $this->equalTo($product->getFinalPrice()),
                $this->equalTo($data['unit_price_incl_tax'])
            )->will($this->returnValue($data['unit_price_incl_tax']));
        $helper->expects($this->any())
            ->method('getHelperModel')
            ->with($this->equalTo('Mage_Tax_Helper_Data'))
            ->will($this->returnValue($taxHelper));

        $result = $helper->getUnitPriceLabel($product);
        $this->assertEquals($expected, $result);
    }

    public function providerGetUnitPriceLabel()
    {
        return array(
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 987,
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), "111 / 321 654 654 short / 123 456 456 short"),
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 987,
                'unit_price_incl_tax' => 55,
                'base_price' => null
            ), " / 321 654 654 short / 123 456 456 short"),
            array(array(
                'unit_price_amount' => 1,
                'unit_price_base_amount' => 2,
                'unit_price_unit' => 3,
                'unit_price_base_unit' => 4,
                'final_price' => 5,
                'unit_price_incl_tax' => 6,
                'base_price' => 7
            ), "7 / 2 4 4 short / 1 3 3 short"),
            array(array(
                'unit_price_amount' => null,
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 987,
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), ""),
            array(array(
                'unit_price_amount' => 'abc',
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 987,
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), ""),
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => null,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 987,
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), ""),
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => "abc",
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 333,
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), ""),
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => '',
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), ""),
            array(array(
                'unit_price_amount' => 123,
                'unit_price_base_amount' => 321,
                'unit_price_unit' => 456,
                'unit_price_base_unit' => 654,
                'final_price' => 'abc',
                'unit_price_incl_tax' => 55,
                'base_price' => 111
            ), "")

        );
    }

    /**
     * @param array $data
     * @param array $expected
     * @dataProvider providerLoadDefaultUnitPriceValues
     */
    public function testLoadDefaultUnitPriceValues(array $data, array $expected)
    {
        $product = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->setMethods(array('addCustomOption'))
            ->disableOriginalConstructor()
            ->getMock();

        $defaultData = array('unit_price_base_amount' => 1, 'unit_price_unit' => 2, 'unit_price_base_unit' => 3);

        foreach ($data as $attributeCode => $value) {
            $product->setData($attributeCode, $value);
            unset($defaultData[$attributeCode]);
        }

        $helper = $this->getMockBuilder('Saas_UnitPrice_Helper_FakeData')
            ->setMethods(array('getModel'))
            ->disableOriginalConstructor()
            ->getMock();

        $attributes = array();
        $frontend = array();
        foreach ($defaultData as $attributeCode => $value) {
            $frontend[$attributeCode] = $this->getMockBuilder('Magento_Eav_Model_Entity_Attribute_Frontend_Abstract')
                ->setMethods(array('getValue'))
                ->disableOriginalConstructor()
                ->getMock();
            $frontend[$attributeCode]->expects($this->any())
                ->method('getValue')
                ->with($this->equalTo($product))
                ->will($this->returnValue($value));

            $attributes[$attributeCode] = $this->getMockBuilder('Magento_Eav_Model_Entity_Attribute')
                ->setMethods(array('loadByCode', 'getFrontend'))
                ->disableOriginalConstructor()
                ->getMock();
            $attributes[$attributeCode]->expects($this->any())
                ->method('loadByCode')
                ->with($this->equalTo('catalog_product'), $this->equalTo($attributeCode))
                ->will($this->returnSelf());
            $attributes[$attributeCode]->expects($this->any())
                ->method('getFrontend')
                ->will($this->returnValue($frontend[$attributeCode]));
        }

        $helper->expects($this->any())
            ->method('getModel')
            ->with($this->equalTo('Magento_Eav_Model_Entity_Attribute'))
            ->will(call_user_func_array(array($this, 'onConsecutiveCalls'), $attributes));

        $helper->_loadDefaultUnitPriceValues($product);
        $this->assertEquals($expected['unit_price_base_amount'], $product->getUnitPriceBaseAmount());
        $this->assertEquals($expected['unit_price_unit'], $product->getUnitPriceUnit());
        $this->assertEquals($expected['unit_price_base_unit'], $product->getUnitPriceBaseUnit());
    }

    public function providerLoadDefaultUnitPriceValues()
    {
        return array(
            array(
                array('unit_price_base_amount' => 1333, 'unit_price_unit' => 999, 'unit_price_base_unit' => 3555),
                array('unit_price_base_amount' => 1333, 'unit_price_unit' => 999, 'unit_price_base_unit' => 3555)
            ),
            array(
                array('unit_price_unit' => 999, 'unit_price_base_unit' => 3555),
                array('unit_price_base_amount' => 1, 'unit_price_unit' => 999, 'unit_price_base_unit' => 3555)
            ),
            array(
                array('unit_price_base_amount' => 1333, 'unit_price_base_unit' => 3555),
                array('unit_price_base_amount' => 1333, 'unit_price_unit' => 2, 'unit_price_base_unit' => 3555)
            ),
            array(
                array('unit_price_base_amount' => 1333),
                array('unit_price_base_amount' => 1333, 'unit_price_unit' => 2, 'unit_price_base_unit' => 3)
            ),
            array(
                array('unit_price_unit' => 1333),
                array('unit_price_base_amount' => 1, 'unit_price_unit' => 1333, 'unit_price_base_unit' => 3)
            ),
            array(
                array(),
                array('unit_price_base_amount' => 1, 'unit_price_unit' => 2, 'unit_price_base_unit' => 3)
            ),
        );
    }
}
