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
namespace Magento\Catalog\Model\Layer;

/**
 * Test class for \Magento\Catalog\Model\Layer.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Category
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Category');
        $this->_model->setCurrentCategory(4);
    }

    public function testGetStateKey()
    {
        $this->assertEquals('STORE_1_CAT_4_CUSTGROUP_0', $this->_model->getStateKey());
    }

    public function testGetProductCollection()
    {
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $this->_model->getProductCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Product\Collection', $collection);
        $ids = $collection->getAllIds();
        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertSame($collection, $this->_model->getProductCollection());
    }

    public function testApply()
    {
        $this->_model->getState()
            ->addFilter(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                    'Magento\Catalog\Model\Layer\Filter\Item',
                    array(
                        'data' => array(
                            'filter' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                ->create(
                                    'Magento\Catalog\Model\Layer\Filter\Category', array('layer' => $this->_model)
                                ),
                            'value'  => 'expected-value-string',
                        )
                    )
                )
            )
            ->addFilter(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                    'Magento\Catalog\Model\Layer\Filter\Item',
                    array(
                        'data' => array(
                            'filter' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                ->create(
                                    'Magento\Catalog\Model\Layer\Filter\Decimal', array('layer' => $this->_model)
                                ),
                            'value'  => 1234,
                        )
                    )
                )
            )
        );

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
        $existingCategory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category'
        );
        $existingCategory->load(5);

        /* Category object */
        /** @var $model \Magento\Catalog\Model\Layer */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Category');
        $model->setCurrentCategory($existingCategory);
        $this->assertSame($existingCategory, $model->getCurrentCategory());

        /* Category id */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Category');
        $model->setCurrentCategory(3);
        $actualCategory = $model->getCurrentCategory();
        $this->assertInstanceOf('Magento\Catalog\Model\Category', $actualCategory);
        $this->assertEquals(3, $actualCategory->getId());
        $this->assertSame($actualCategory, $model->getCurrentCategory());

        /* Category in registry */
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->register('current_category', $existingCategory);
        try {
            $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Layer\Category');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
            $objectManager->get('Magento\Registry')->unregister('current_category');
            $this->assertSame($existingCategory, $model->getCurrentCategory());
        } catch (\Exception $e) {
            $objectManager->get('Magento\Registry')->unregister('current_category');
            throw $e;
        }


        try {
            $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Layer\Category');
            $model->setCurrentCategory(new \Magento\Object());
            $this->fail('Assign category of invalid class.');
        } catch (\Magento\Core\Exception $e) {
        }

        try {
            $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Layer\Category');
            $model->setCurrentCategory(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Category'));
            $this->fail('Assign category with invalid id.');
        } catch (\Magento\Core\Exception $e) {
        }
    }

    public function testGetCurrentStore()
    {
        $this->assertSame(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Core\Model\StoreManagerInterface'
            )->getStore(),
            $this->_model->getCurrentStore()
        );
    }

    public function testGetState()
    {
        $state = $this->_model->getState();
        $this->assertInstanceOf('Magento\Catalog\Model\Layer\State', $state);
        $this->assertSame($state, $this->_model->getState());

        $state = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Layer\State'
        );
        $this->_model->setState($state);
        // $this->_model->setData('state', state);
        $this->assertSame($state, $this->_model->getState());
    }
}
