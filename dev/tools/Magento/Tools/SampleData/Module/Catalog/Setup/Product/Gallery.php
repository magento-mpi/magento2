<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup\Product;

use Magento\Framework\App\Filesystem;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Catalog\Model\Resource\Product\Attribute\Backend\Media as GalleryAttribute;

/**
 * Class Gallery
 */
class Gallery
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var array
     */
    protected $images;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var array
     */
    protected $fixtures = [
        'Catalog/SimpleProduct/images_gear_bags.csv',
        'Catalog/SimpleProduct/images_gear_fitness_equipment.csv',
        'Catalog/SimpleProduct/images_gear_watches.csv'
    ];

    /**
     * @var false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected $mediaAttribute;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param ProductFactory $productFactory
     * @param GalleryAttribute $galleryAttribute
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        ProductFactory $productFactory,
        GalleryAttribute $galleryAttribute,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->galleryAttribute = $galleryAttribute;
        $this->productFactory = $productFactory;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->mediaAttribute = $eavConfig->getAttribute('catalog_product', 'media_gallery');
        $this->loadFixtures();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function install($product)
    {
        if (!empty($this->images[$product->getSku()])) {
            $this->storeImage($product, $this->images[$product->getSku()]);
        } else {
            $this->errors[] = $product->getSku();
        }
    }

    /**
     * Save image information to DB.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $images
     */
    protected function storeImage($product, $images)
    {
        $baseImage = '';
        $i = 1;
        foreach ($images as $image) {
            if (empty($image)) {
                $this->errors[] = $product->getSku();
                continue;
            }
            if (strpos($image, '_main') !== false) {
                $baseImage = $image;
            }
            $id = $this->galleryAttribute->insertGallery(array(
                'attribute_id' => $this->mediaAttribute->getAttributeId(),
                'entity_id' => $product->getId(),
                'value' => $image
            ));
            $this->galleryAttribute->insertGalleryValueInStore(array(
                'value_id' => $id,
                'store_id' => 0,
                'label' => 'Image',
                'position' => $i,
                'disables' => 0
            ));
            $i++;
        }

        if (empty($baseImage)) {
            $baseImage = $images[0];
        }

        if ($baseImage) {
            $imageAttribute = $product->getResource()->getAttribute('image');
            $smallImageAttribute = $product->getResource()->getAttribute('small_image');
            $thumbnailAttribute = $product->getResource()->getAttribute('thumbnail');
            $adapter = $product->getResource()->getWriteConnection();
            foreach ([$imageAttribute, $smallImageAttribute, $thumbnailAttribute] as $attribute) {
                $table = $imageAttribute->getBackend()->getTable();
                /** @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter*/
                $data = array(
                    'entity_type_id' => $product->getEntityTypeId(),
                    $attribute->getBackend()->getEntityIdField() => $product->getId(),
                    'attribute_id' => $attribute->getId(),
                    'value' => $baseImage
                );
                $adapter->insertOnDuplicate($table, $data, array('value'));
            }
        }
    }

    /**
     * Set fixtures
     * @param array $fixtures
     */
    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
        $this->loadFixtures();
    }

    /**
     * Load data from fixtures
     */
    protected function loadFixtures()
    {
        $this->images = [];
        foreach ($this->fixtures as $file) {
            /** @var \Magento\Framework\File\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                $this->images[$row['sku']][] = $row['image'];
            }
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (!empty($this->errors)) {
            echo 'No images found for: ' . PHP_EOL . implode(',', $this->errors) . PHP_EOL;
        }
    }
}
