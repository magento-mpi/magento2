<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_productType;

    protected function setUp()
    {
        $this->_productType = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Catalog_Model_Product_Type');
    }

    /**
     * @param sring|null $typeId
     * @param string $expectedClass
     * @dataProvider factoryDataProvider
     */
    public function testFactory($typeId, $expectedClass)
    {
        $product = new Magento_Object;
        if ($typeId) {
            $product->setTypeId($typeId);
        }
        $type = $this->_productType->factory($product);
        $this->assertInstanceOf($expectedClass, $type);
    }

    /**
     * @return array
     */
    public function factoryDataProvider()
    {
        return array(
            array(null, 'Magento_Catalog_Model_Product_Type_Simple'),
            array(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE, 'Magento_Catalog_Model_Product_Type_Simple'),
            array(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, 'Magento_Catalog_Model_Product_Type_Virtual'),
            array(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, 'Magento_Catalog_Model_Product_Type_Grouped'),
            array(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                'Magento_Catalog_Model_Product_Type_Configurable'
            ),
            array(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, 'Magento_Bundle_Model_Product_Type'),
            array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
                'Magento_Downloadable_Model_Product_Type'
            ),
        );
    }

    /**
     * @param sring|null $typeId
     * @dataProvider factoryReturnsSingletonDataProvider
     */
    public function testFactoryReturnsSingleton($typeId)
    {
        $product = new Magento_Object;
        if ($typeId) {
            $product->setTypeId($typeId);
        }

        $type = $this->_productType->factory($product);
        $otherType = $this->_productType->factory($product);
        $this->assertSame($otherType, $type);
    }

    /**
     * @return array
     */
    public function factoryReturnsSingletonDataProvider()
    {
        return array(
            array(null),
            array(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE),
            array(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL),
            array(Magento_Catalog_Model_Product_Type::TYPE_GROUPED),
            array(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE),
            array(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE),
            array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
        );
    }

    /**
     * @param sring|null $typeId
     * @param string $expectedClass
     * @dataProvider priceFactoryDataProvider
     */
    public function testPriceFactory($typeId, $expectedClass)
    {
        $type = $this->_productType->priceFactory($typeId);
        $this->assertInstanceOf($expectedClass, $type);
    }

    public function priceFactoryDataProvider()
    {
        return array(
            array(null, 'Magento_Catalog_Model_Product_Type_Price'),
            array(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE, 'Magento_Catalog_Model_Product_Type_Price'),
            array(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, 'Magento_Catalog_Model_Product_Type_Price'),
            array(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, 'Magento_Catalog_Model_Product_Type_Price'),
            array(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                'Magento_Catalog_Model_Product_Type_Configurable_Price'
            ),
            array(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, 'Magento_Bundle_Model_Product_Price'),
            array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
                'Magento_Downloadable_Model_Product_Price'
            ),
        );
    }

    public function testGetOptionArray()
    {
        $options = $this->_productType->getOptionArray();
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE, $options);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, $options);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, $options);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $options);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, $options);
        $this->assertArrayHasKey(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $options);
    }

    public function testGetAllOption()
    {
        $options = $this->_productType->getAllOption();
        $this->assertTrue(isset($options[0]['value']));
        $this->assertTrue(isset($options[0]['label']));
        // doesn't make sense to test other values, because the structure of resulting array is inconsistent
    }

    public function testGetAllOptions()
    {
        $options = $this->_productType->getAllOptions();
        $types = $this->_assertOptions($options);
        $this->assertContains('', $types);
    }

    public function testGetOptions()
    {
        $options = $this->_productType->getOptions();
        $this->_assertOptions($options);
    }

    /**
     * @param string $typeId
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($typeId)
    {
        $this->assertNotEmpty($this->_productType->getOptionText($typeId));
    }

    public function getOptionTextDataProvider()
    {
        return array(
            array(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE),
            array(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL),
            array(Magento_Catalog_Model_Product_Type::TYPE_GROUPED),
            array(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE),
            array(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE),
            array(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE),
        );
    }

    public function testGetTypes()
    {
        $types = $this->_productType->getTypes();
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE, $types);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, $types);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, $types);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $types);
        $this->assertArrayHasKey(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, $types);
        $this->assertArrayHasKey(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('label', $type);
            $this->assertArrayHasKey('model', $type);
            $this->assertArrayHasKey('composite', $type);
            // possible bug: index_priority is not defined for each type
        }
    }

    public function testGetCompositeTypes()
    {
        $types = $this->_productType->getCompositeTypes();
        $this->assertInternalType('array', $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, $types);
    }

    public function testGetTypesByPriority()
    {
        $types = $this->_productType->getTypesByPriority();
        // collect the types and priority in the same order as the method returns
        $result = array();
        foreach ($types as $typeId => $type) {
            if (!isset($type['index_priority'])) { // possible bug: index_priority is not defined for each type
                $priority = 0;
            } else {
                $priority = (int)$type['index_priority'];
            }
            // non-composite must be before composite
            if (1 != $type['composite']) {
                $priority = -1 * $priority;
            }
            $result[$typeId] = $priority;
        }

        $expectedResult = $result;
        asort($expectedResult);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Perform assertions on type "options" structure
     *
     * @param array $options
     * @return array collected types found in options
     */
    protected function _assertOptions($options)
    {
        $this->assertInternalType('array', $options);
        $types = array();
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $types[] = $option['value'];
        }
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_GROUPED, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $types);
        $this->assertContains(Magento_Catalog_Model_Product_Type::TYPE_BUNDLE, $types);
        $this->assertContains(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $types);
        return $types;
    }
}
