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

/**
 * Test class for \Magento\Catalog\Model\Layer.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Model_LayerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('\Magento\Catalog\Model\Layer');
        $this->_model->setCurrentCategory(4);
    }

    public function testGetStateKey()
    {
        $this->assertEquals('STORE_1_CAT_4_CUSTGROUP_0', $this->_model->getStateKey());
    }

    public function testGetStateTags()
    {
        $this->assertEquals(array('catalog_category4'), $this->_model->getStateTags());
        $this->assertEquals(
            array('additional_state_tag1', 'additional_state_tag2', 'catalog_category4'),
            $this->_model->getStateTags(array('additional_state_tag1', 'additional_state_tag2'))
        );
    }

    public function testGetProductCollection()
    {
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $this->_model->getProductCollection();
        $this->assertInstanceOf('\Magento\Catalog\Model\Resource\Product\Collection', $collection);
        $ids = $collection->getAllIds();
        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertSame($collection, $this->_model->getProductCollection());
    }

    public function testApply()
    {
        $this->_model->getState()
            ->addFilter(
                Mage::getModel(
                    '\Magento\Catalog\Model\Layer\Filter\Item',
                    array(
                        'data' => array(
                            'filter' => Mage::getModel('\Magento\Catalog\Model\Layer\Filter\Category'),
                            'value'  => 'expected-value-string',
                        )
                    )
                )
            )
            ->addFilter(
                Mage::getModel(
                    '\Magento\Catalog\Model\Layer\Filter\Item',
                    array(
                        'data' => array(
                            'filter' => Mage::getModel('\Magento\Catalog\Model\Layer\Filter\Decimal'),
                            'value'  => 1234,
                        )
                    )
                )
            )
        ;

        $this->_model->apply();
        $this->assertEquals(
            'STORE_1_CAT_4_CUSTGROUP_0_cat_expected-value-string_decimal_1234',
            $this->_model->getStateKey()
        );

        $this->_model->apply();
        $this->assertEquals(
            'STORE_1_CAT_4_CUSTGROUP_0_cat_expected-value-string_decimal_1234_cat_expected-value-string_decimal_1234',
            $this->_model->getStateKey()
        );
    }

    public function testGetSetCurrentCategory()
    {
        $existingCategory = Mage::getModel('\Magento\Catalog\Model\Category');
        $existingCategory->load(5);

        /* Category object */
        /** @var $model \Magento\Catalog\Model\Layer */
        $model = Mage::getModel('\Magento\Catalog\Model\Layer');
        $model->setCurrentCategory($existingCategory);
        $this->assertSame($existingCategory, $model->getCurrentCategory());

        /* Category id */
        $model = Mage::getModel('\Magento\Catalog\Model\Layer');
        $model->setCurrentCategory(3);
        $actualCategory = $model->getCurrentCategory();
        $this->assertInstanceOf('\Magento\Catalog\Model\Category', $actualCategory);
        $this->assertEquals(3, $actualCategory->getId());
        $this->assertSame($actualCategory, $model->getCurrentCategory());

        /* Category in registry */
        Mage::register('current_category', $existingCategory);
        try {
            $model = Mage::getModel('\Magento\Catalog\Model\Layer');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
            Mage::unregister('current_category');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
        } catch (Exception $e) {
            Mage::unregister('current_category');
            throw $e;
        }


        try {
            $model = Mage::getModel('\Magento\Catalog\Model\Layer');
            $model->setCurrentCategory(new \Magento\Object());
            $this->fail('Assign category of invalid class.');
        } catch (\Magento\Core\Exception $e) {
        }

        try {
            $model = Mage::getModel('\Magento\Catalog\Model\Layer');
            $model->setCurrentCategory(Mage::getModel('\Magento\Catalog\Model\Category'));
            $this->fail('Assign category with invalid id.');
        } catch (\Magento\Core\Exception $e) {
        }
    }

    public function testGetCurrentStore()
    {
        $this->assertSame(Mage::app()->getStore(), $this->_model->getCurrentStore());
    }

    public function testGetFilterableAttributes()
    {
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->_model->getFilterableAttributes();
        $this->assertInstanceOf('\Magento\Catalog\Model\Resource\Product\Attribute\Collection', $collection);

        $items = $collection->getItems();
        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items), 'Number of items in collection.');

        $this->assertInstanceOf('\Magento\Catalog\Model\Resource\Eav\Attribute', $collection->getFirstItem());
        $this->assertEquals('price', $collection->getFirstItem()->getAttributeCode());

        //$this->assertNotSame($collection, $this->_model->getFilterableAttributes());
    }

    public function testGetState()
    {
        $state = $this->_model->getState();
        $this->assertInstanceOf('\Magento\Catalog\Model\Layer\State', $state);
        $this->assertSame($state, $this->_model->getState());

        $state = Mage::getModel('\Magento\Catalog\Model\Layer\State');
        $this->_model->setState($state); // $this->_model->setData('state', state);
        $this->assertSame($state, $this->_model->getState());
    }
}
