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

namespace Magento\Catalog\Model\Product;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param sring|null $typeId
     * @param string $expectedClass
     * @dataProvider factoryDataProvider
     */
    public function testFactory($typeId, $expectedClass)
    {
        $product = new \Magento\Object;
        if ($typeId) {
            $product->setTypeId($typeId);
        }
        $type = \Magento\Catalog\Model\Product\Type::factory($product);
        $this->assertInstanceOf($expectedClass, $type);
    }

    /**
     * @return array
     */
    public function factoryDataProvider()
    {
        return array(
            array(null, 'Magento\Catalog\Model\Product\Type\Simple'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, 'Magento\Catalog\Model\Product\Type\Simple'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, 'Magento\Catalog\Model\Product\Type\Virtual'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, 'Magento\Catalog\Model\Product\Type\Grouped'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
                'Magento\Catalog\Model\Product\Type\Configurable'
            ),
            array(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, 'Magento\Bundle\Model\Product\Type'),
            array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
                'Magento\Downloadable\Model\Product\Type'
            ),
        );
    }

    /**
     * @param sring|null $typeId
     * @dataProvider factoryReturnsSingletonDataProvider
     */
    public function testFactoryReturnsSingleton($typeId)
    {
        $product = new \Magento\Object;
        if ($typeId) {
            $product->setTypeId($typeId);
        }

        $type = \Magento\Catalog\Model\Product\Type::factory($product);
        $otherType = \Magento\Catalog\Model\Product\Type::factory($product);
        $this->assertSame($otherType, $type);
    }

    /**
     * @return array
     */
    public function factoryReturnsSingletonDataProvider()
    {
        return array(
            array(null),
            array(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE),
            array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL),
            array(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED),
            array(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE),
            array(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE),
            array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
        );
    }

    /**
     * @param sring|null $typeId
     * @param string $expectedClass
     * @dataProvider priceFactoryDataProvider
     */
    public function testPriceFactory($typeId, $expectedClass)
    {
        $type = \Magento\Catalog\Model\Product\Type::priceFactory($typeId);
        $this->assertInstanceOf($expectedClass, $type);
    }

    public function priceFactoryDataProvider()
    {
        return array(
            array(null, 'Magento\Catalog\Model\Product\Type\Price'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, 'Magento\Catalog\Model\Product\Type\Price'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, 'Magento\Catalog\Model\Product\Type\Price'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, 'Magento\Catalog\Model\Product\Type\Price'),
            array(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
                'Magento\Catalog\Model\Product\Type\Configurable\Price'
            ),
            array(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, 'Magento\Bundle\Model\Product\Price'),
            array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
                'Magento\Downloadable\Model\Product\Price'
            ),
        );
    }

    public function testGetOptionArray()
    {
        $options = \Magento\Catalog\Model\Product\Type::getOptionArray();
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, $options);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, $options);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, $options);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE, $options);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, $options);
        $this->assertArrayHasKey(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $options);
    }

    public function testGetAllOption()
    {
        $options = \Magento\Catalog\Model\Product\Type::getAllOption();
        $this->assertTrue(isset($options[0]['value']));
        $this->assertTrue(isset($options[0]['label']));
        // doesn't make sense to test other values, because the structure of resulting array is inconsistent
    }

    public function testGetAllOptions()
    {
        $options = \Magento\Catalog\Model\Product\Type::getAllOptions();
        $types = $this->_assertOptions($options);
        $this->assertContains('', $types);
    }

    public function testGetOptions()
    {
        $options = \Magento\Catalog\Model\Product\Type::getOptions();
        $this->_assertOptions($options);
    }

    /**
     * @param string $typeId
     * @dataProvider getOptionTextDataProvider
     */
    public function testGetOptionText($typeId)
    {
        $this->assertNotEmpty(\Magento\Catalog\Model\Product\Type::getOptionText($typeId));
    }

    public function getOptionTextDataProvider()
    {
        return array(
            array(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE),
            array(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL),
            array(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED),
            array(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE),
            array(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE),
            array(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE),
        );
    }

    public function testGetTypes()
    {
        $types = \Magento\Catalog\Model\Product\Type::getTypes();
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, $types);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, $types);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, $types);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE, $types);
        $this->assertArrayHasKey(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, $types);
        $this->assertArrayHasKey(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('label', $type);
            $this->assertArrayHasKey('model', $type);
            $this->assertArrayHasKey('composite', $type);
            // possible bug: index_priority is not defined for each type
        }
    }

    public function testGetCompositeTypes()
    {
        $types = \Magento\Catalog\Model\Product\Type::getCompositeTypes();
        $this->assertInternalType('array', $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, $types);
    }

    public function testGetTypesByPriority()
    {
        $types = \Magento\Catalog\Model\Product\Type::getTypesByPriority();

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
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE, $types);
        $this->assertContains(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE, $types);
        $this->assertContains(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE, $types);
        return $types;
    }
}
