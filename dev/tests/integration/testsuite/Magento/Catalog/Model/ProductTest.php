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
namespace Magento\Catalog\Model;

/**
 * Tests product model:
 * - general behaviour is tested (external interaction and pricing is not tested there)
 *
 * @see \Magento\Catalog\Model\ProductExternalTest
 * @see \Magento\Catalog\Model\ProductPriceTest
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
    }

    public static function tearDownAfterClass()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Catalog\Model\Product\Media\Config $config */
        $config = $objectManager->get('Magento\Catalog\Model\Product\Media\Config');

        /** @var \Magento\Filesystem\Directory\WriteInterface $mediaDirectory */
        $mediaDirectory = $objectManager->get(
            'Magento\App\Filesystem'
        )->getDirectoryWrite(
            \Magento\App\Filesystem::MEDIA_DIR
        );

        if ($mediaDirectory->isExist($config->getBaseMediaPath())) {
            $mediaDirectory->delete($config->getBaseMediaPath());
        }
        if ($mediaDirectory->isExist($config->getBaseTmpMediaPath())) {
            $mediaDirectory->delete($config->getBaseTmpMediaPath());
        }
    }

    public function testCanAffectOptions()
    {
        $this->assertFalse($this->_model->canAffectOptions());
        $this->_model->canAffectOptions(true);
        $this->assertTrue($this->_model->canAffectOptions());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     */
    public function testCRUD()
    {
        $this->_model->setTypeId(
            'simple'
        )->setAttributeSetId(
            4
        )->setName(
            'Simple Product'
        )->setSku(
            uniqid()
        )->setPrice(
            10
        )->setMetaTitle(
            'meta title'
        )->setMetaKeyword(
            'meta keyword'
        )->setMetaDescription(
            'meta description'
        )->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
        )->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );
        $crud = new \Magento\TestFramework\Entity($this->_model, array('sku' => uniqid()));
        $crud->testCrud();
    }

    public function testCleanCache()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\App'
        )->saveCache(
            'test',
            'catalog_product_999',
            array('catalog_product_999')
        );
        // potential bug: it cleans by cache tags, generated from its ID, which doesn't make much sense
        $this->_model->setId(999)->cleanCache();
        $this->assertFalse(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Core\Model\App'
            )->loadCache(
                'catalog_product_999'
            )
        );
    }

    public function testAddImageToMediaGallery()
    {
        // Model accepts only files in tmp media path, we need to copy fixture file there
        $mediaFile = $this->_copyFileToBaseTmpMediaPath(dirname(__DIR__) . '/_files/magento_image.jpg');

        $this->_model->addImageToMediaGallery($mediaFile);
        $gallery = $this->_model->getData('media_gallery');
        $this->assertNotEmpty($gallery);
        $this->assertTrue(isset($gallery['images'][0]['file']));
        $this->assertStringStartsWith('/m/a/magento_image', $gallery['images'][0]['file']);
        $this->assertTrue(isset($gallery['images'][0]['position']));
        $this->assertTrue(isset($gallery['images'][0]['disabled']));
        $this->assertArrayHasKey('label', $gallery['images'][0]);
    }

    /**
     * Copy file to media tmp directory and return it's name
     *
     * @param string $sourceFile
     * @return string
     */
    protected function _copyFileToBaseTmpMediaPath($sourceFile)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Catalog\Model\Product\Media\Config $config */
        $config = $objectManager->get('Magento\Catalog\Model\Product\Media\Config');

        /** @var \Magento\Filesystem\Directory\WriteInterface $mediaDirectory */
        $mediaDirectory = $objectManager->get(
            'Magento\App\Filesystem'
        )->getDirectoryWrite(
            \Magento\App\Filesystem::MEDIA_DIR
        );

        $mediaDirectory->create($config->getBaseTmpMediaPath());
        $targetFile = $config->getTmpMediaPath(basename($sourceFile));
        copy($sourceFile, $mediaDirectory->getAbsolutePath($targetFile));

        return $targetFile;
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     */
    public function testDuplicate()
    {
        $this->_model->load(1);
        // fixture
        /** @var \Magento\Catalog\Model\Product\Copier $copier */
        $copier = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Product\Copier'
        );
        $duplicate = $copier->copy($this->_model);
        try {
            $this->assertNotEmpty($duplicate->getId());
            $this->assertNotEquals($duplicate->getId(), $this->_model->getId());
            $this->assertNotEquals($duplicate->getSku(), $this->_model->getSku());
            $this->assertEquals(
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED,
                $duplicate->getStatus()
            );
            $this->assertEquals(\Magento\Core\Model\Store::DEFAULT_STORE_ID, $duplicate->getStoreId());
            $this->_undo($duplicate);
        } catch (\Exception $e) {
            $this->_undo($duplicate);
            throw $e;
        }
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testDuplicateSkuGeneration()
    {
        $this->_model->load(1);
        $this->assertEquals('simple', $this->_model->getSku());
        /** @var \Magento\Catalog\Model\Product\Copier $copier */
        $copier = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Catalog\Model\Product\Copier'
        );
        $duplicate = $copier->copy($this->_model);
        $this->assertEquals('simple-3', $duplicate->getSku());
    }

    /**
     * Delete model
     *
     * @param \Magento\Core\Model\AbstractModel $duplicate
     */
    protected function _undo($duplicate)
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\StoreManagerInterface'
        )->getStore()->setId(
            \Magento\Core\Model\Store::DEFAULT_STORE_ID
        );
        $duplicate->delete();
    }

    /**
     * @covers \Magento\Catalog\Model\Product::getVisibleInCatalogStatuses
     * @covers \Magento\Catalog\Model\Product::getVisibleStatuses
     * @covers \Magento\Catalog\Model\Product::isVisibleInCatalog
     * @covers \Magento\Catalog\Model\Product::getVisibleInSiteVisibilities
     * @covers \Magento\Catalog\Model\Product::isVisibleInSiteVisibility
     */
    public function testVisibilityApi()
    {
        $this->assertEquals(
            array(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED),
            $this->_model->getVisibleInCatalogStatuses()
        );
        $this->assertEquals(
            array(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED),
            $this->_model->getVisibleStatuses()
        );

        $this->_model->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        $this->assertFalse($this->_model->isVisibleInCatalog());

        $this->_model->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $this->assertTrue($this->_model->isVisibleInCatalog());

        $this->assertEquals(
            array(
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
            ),
            $this->_model->getVisibleInSiteVisibilities()
        );

        $this->assertFalse($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
        $this->_model->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $this->assertTrue($this->_model->isVisibleInSiteVisibility());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::isDuplicable
     * @covers \Magento\Catalog\Model\Product::setIsDuplicable
     */
    public function testIsDuplicable()
    {
        $this->assertTrue($this->_model->isDuplicable());
        $this->_model->setIsDuplicable(0);
        $this->assertFalse($this->_model->isDuplicable());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::isSalable
     * @covers \Magento\Catalog\Model\Product::isSaleable
     * @covers \Magento\Catalog\Model\Product::isAvailable
     * @covers \Magento\Catalog\Model\Product::isInStock
     */
    public function testIsSalable()
    {
        $this->_model->load(1);
        // fixture
        $this->assertTrue((bool)$this->_model->isSalable());
        $this->assertTrue((bool)$this->_model->isSaleable());
        $this->assertTrue((bool)$this->_model->isAvailable());
        $this->assertTrue($this->_model->isInStock());
        $this->_model->setStatus(0);
        $this->assertFalse((bool)$this->_model->isSalable());
        $this->assertFalse((bool)$this->_model->isSaleable());
        $this->assertFalse((bool)$this->_model->isAvailable());
        $this->assertFalse($this->_model->isInStock());
    }

    /**
     * @covers \Magento\Catalog\Model\Product::isVirtual
     * @covers \Magento\Catalog\Model\Product::getIsVirtual
     */
    public function testIsVirtual()
    {
        $this->assertFalse($this->_model->isVirtual());
        $this->assertFalse($this->_model->getIsVirtual());

        /** @var $model \Magento\Catalog\Model\Product */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product',
            array('data' => array('type_id' => \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL))
        );
        $this->assertTrue($model->isVirtual());
        $this->assertTrue($model->getIsVirtual());
    }

    public function testToArray()
    {
        $this->assertEquals(array(), $this->_model->toArray());
        $this->_model->setSku('sku')->setName('name');
        $this->assertEquals(array('sku' => 'sku', 'name' => 'name'), $this->_model->toArray());
    }

    public function testFromArray()
    {
        $this->_model->fromArray(array('sku' => 'sku', 'name' => 'name', 'stock_item' => array('key' => 'value')));
        $this->assertEquals(array('sku' => 'sku', 'name' => 'name'), $this->_model->getData());
    }

    /**
     * @param bool $isUserDefined
     * @param string $code
     * @param bool $expectedResult
     * @dataProvider isReservedAttributeDataProvider
     */
    public function testIsReservedAttribute($isUserDefined, $code, $expectedResult)
    {
        $attribute = new \Magento\Object(array('is_user_defined' => $isUserDefined, 'attribute_code' => $code));
        $this->assertEquals($expectedResult, $this->_model->isReservedAttribute($attribute));
    }

    public function isReservedAttributeDataProvider()
    {
        return array(
            array(true, 'position', true),
            array(true, 'type_id', false),
            array(false, 'no_difference', false)
        );
    }

    /**
     * @magentoAppArea frontend
     */
    public function testSetOrigDataFrontend()
    {
        $this->assertEmpty($this->_model->getOrigData());
        $this->_model->setOrigData('key', 'value');
        $this->assertEmpty($this->_model->getOrigData());
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testSetOrigDataBackend()
    {
        $this->assertEmpty($this->_model->getOrigData());
        $this->_model->setOrigData('key', 'value');
        $this->assertEquals('value', $this->_model->getOrigData('key'));
    }

    /**
     * @magentoAppArea frontend
     */
    public function testReset()
    {
        $model = $this->_model;

        $this->_assertEmpty($model);

        $this->_model->setData('key', 'value');
        $this->_model->reset();
        $this->_assertEmpty($model);

        $this->_model->setOrigData('key', 'value');
        $this->_model->reset();
        $this->_assertEmpty($model);

        $this->_model->addCustomOption('key', 'value');
        $this->_model->reset();
        $this->_assertEmpty($model);

        $this->_model->addOption(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product\Option')
        );
        $this->_model->reset();
        $this->_assertEmpty($model);

        $this->_model->canAffectOptions(true);
        $this->_model->reset();
        $this->_assertEmpty($model);
    }

    /**
     * Check is model empty or not
     *
     * @param \Magento\Core\Model\AbstractModel $model
     */
    protected function _assertEmpty($model)
    {
        $this->assertEquals(array(), $model->getData());
        $this->assertEquals(null, $model->getOrigData());
        $this->assertEquals(array(), $model->getCustomOptions());
        // impossible to test $_optionInstance
        $this->assertEquals(array(), $model->getOptions());
        $this->assertFalse($model->canAffectOptions());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testIsProductsHasSku()
    {
        $this->assertTrue($this->_model->isProductsHasSku(array(10, 11)));
    }

    public function testProcessBuyRequest()
    {
        $request = new \Magento\Object();
        $result = $this->_model->processBuyRequest($request);
        $this->assertInstanceOf('Magento\Object', $result);
        $this->assertArrayHasKey('errors', $result->getData());
    }

    public function testValidate()
    {
        $this->_model->setTypeId(
            'simple'
        )->setAttributeSetId(
            4
        )->setName(
            'Simple Product'
        )->setSku(
            uniqid('', true) . uniqid('', true) . uniqid('', true)
        )->setPrice(
            10
        )->setMetaTitle(
            'meta title'
        )->setMetaKeyword(
            'meta keyword'
        )->setMetaDescription(
            'meta description'
        )->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
        )->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        )->setCollectExceptionMessages(
            true
        );
        $validationResult = $this->_model->validate();
        $this->assertEquals('SKU length should be 64 characters maximum.', $validationResult['sku']);
        unset($validationResult['sku']);
        foreach ($validationResult as $error) {
            $this->assertTrue($error);
        }
    }
}
