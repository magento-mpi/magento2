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
 * Test class for Magento_Catalog_Model_Layer.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Model_LayerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Layer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
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
        /** @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = $this->_model->getProductCollection();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Collection', $collection);
        $ids = $collection->getAllIds();
        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertSame($collection, $this->_model->getProductCollection());
    }

    public function testApply()
    {
        $this->_model->getState()
            ->addFilter(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create(
                    'Magento_Catalog_Model_Layer_Filter_Item',
                    array(
                        'data' => array(
                            'filter' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer_Filter_Category'),
                            'value'  => 'expected-value-string',
                        )
                    )
                )
            )
            ->addFilter(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create(
                    'Magento_Catalog_Model_Layer_Filter_Item',
                    array(
                        'data' => array(
                            'filter' => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer_Filter_Decimal'),
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
        $existingCategory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Category');
        $existingCategory->load(5);

        /* Category object */
        /** @var $model Magento_Catalog_Model_Layer */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
        $model->setCurrentCategory($existingCategory);
        $this->assertSame($existingCategory, $model->getCurrentCategory());

        /* Category id */
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
        $model->setCurrentCategory(3);
        $actualCategory = $model->getCurrentCategory();
        $this->assertInstanceOf('Magento_Catalog_Model_Category', $actualCategory);
        $this->assertEquals(3, $actualCategory->getId());
        $this->assertSame($actualCategory, $model->getCurrentCategory());

        /* Category in registry */
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_category', $existingCategory);
        try {
            $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
            $objectManager->get('Magento_Core_Model_Registry')->unregister('current_category');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
        } catch (Exception $e) {
            $objectManager->get('Magento_Core_Model_Registry')->unregister('current_category');
            throw $e;
        }


        try {
            $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
            $model->setCurrentCategory(new Magento_Object());
            $this->fail('Assign category of invalid class.');
        } catch (Magento_Core_Exception $e) {
        }

        try {
            $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer');
            $model->setCurrentCategory(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Category'));
            $this->fail('Assign category with invalid id.');
        } catch (Magento_Core_Exception $e) {
        }
    }

    public function testGetCurrentStore()
    {
        $this->assertSame(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
                ->getStore(),
            $this->_model->getCurrentStore()
        );
    }

    public function testGetFilterableAttributes()
    {
        /** @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = $this->_model->getFilterableAttributes();
        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Product_Attribute_Collection', $collection);

        $items = $collection->getItems();
        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items), 'Number of items in collection.');

        $this->assertInstanceOf('Magento_Catalog_Model_Resource_Eav_Attribute', $collection->getFirstItem());
        $this->assertEquals('price', $collection->getFirstItem()->getAttributeCode());

        //$this->assertNotSame($collection, $this->_model->getFilterableAttributes());
    }

    public function testGetState()
    {
        $state = $this->_model->getState();
        $this->assertInstanceOf('Magento_Catalog_Model_Layer_State', $state);
        $this->assertSame($state, $this->_model->getState());

        $state = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Layer_State');
        $this->_model->setState($state); // $this->_model->setData('state', state);
        $this->assertSame($state, $this->_model->getState());
    }
}
