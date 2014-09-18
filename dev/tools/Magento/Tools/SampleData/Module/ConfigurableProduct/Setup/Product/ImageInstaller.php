<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct\Setup\Product;

use Magento\Catalog\Service\V1\Product\Attribute\Media\WriteServiceInterface;
use Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryBuilder;
use Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryContentBuilder;
use Magento\Framework\App\Filesystem;

class ImageInstaller
{
    protected $writeService;

    protected $galleryEntryBuilder;

    protected $galleryEntryContentBuilder;

    protected $mediaConfig;

    protected $filesystem;

    protected $colorMap = [
        'AQ' => 'Aqua',
        'BB' => 'Black/Blue',
        'BC' => 'Brown/Cream',
        'BG' => 'Blue/Green',
        'BK' => 'Black',
        'BKG' => 'Black/Gray',
        'BKR' => 'Black/Red',
        'BL' => 'Blue',
        'BM' => 'Black/Mint',
        'BP' => 'Black/Pink',
        'BR' => 'Brown',
        'BT' => 'Black/Teal',
        'BU' => 'Blue',
        'BW' => 'Black/White',
        'CA' => 'Cayenne',
        'CN' => 'Cream/Navy',
        'CR' => 'Cream',
        'DG' => 'Dark Gray',
        'DP' => 'Deep Pink',
        'EB' => 'Electric Blue',
        'GB' => 'Gray/Black',
        'GBT' => 'Gold/Black/Turquoise',
        'GG' => 'Green/Gray',
        'GM' => 'Gray/Mint',
        'GN' => 'Green',
        'GP' => 'Gray/Pink',
        'GR' => 'Gray',
        'GS' => 'Gray/Seafoam',
        'GW' => 'Gray/White',
        'IV' => 'Ivory',
        'KH' => 'Khaki',
        'LB' => 'Light Blue',
        'LE' => 'Lemon',
        'LG' => 'Light Gray',
        'LI' => 'Lime',
        'LM' => 'Lime',
        'LO' => 'Loden',
        'LR' => 'Light Blue/Royal',
        'LV' => 'Lavender',
        'MI' => 'Mint',
        'NA' => 'Navy',
        'NL' => 'Navy/Lime',
        'NV' => 'Navy',
        'NW' => 'Navy/White',
        'OR' => 'Orange',
        'PB' => 'Purple/Light Blue',
        'PE' => 'Periwinkle',
        'PG' => 'Purple/Light Green',
        'PI' => 'Pink',
        'PK' => 'Pink',
        'PM' => 'Purple/Mint',
        'PR' => 'Purple',
        'PU' => 'Purple',
        'RB' => 'Royal',
        'RD' => 'Red',
        'RE' => 'Red',
        'RG' => 'Red/Gray',
        'RO' => 'Royal',
        'RP' => 'Red/Pink',
        'SA' => 'Sand',
        'SE' => 'Seafoam',
        'SG' => 'Salmon/Gold',
        'SI' => 'Silver',
        'SL' => 'Slate',
        'SN' => 'Salmon',
        'TA' => 'Tangerine',
        'TE' => 'Teal',
        'TL' => 'Teal/Lime',
        'TP' => 'Taupe',
        'TS' => 'Teal/Silver',
        'VL' => 'Lavender',
        'VT' => 'Lavender',
        'WH' => 'White',
        'WN' => 'White/Navy',
        'WT' => 'White',
        'YE' => 'Yellow',
        'YL' => 'Yellow',
    ];

    public function __construct(
        WriteServiceInterface $writeService,
        GalleryEntryBuilder $galleryEntryBuilder,
        GalleryEntryContentBuilder $galleryEntryContentBuilder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        Filesystem $filesystem
    ) {
        $this->writeService = $writeService;
        $this->galleryEntryBuilder = $galleryEntryBuilder;
        $this->galleryEntryContentBuilder = $galleryEntryContentBuilder;
        $this->mediaConfig = $mediaConfig;
        $this->filesystem = $filesystem;
    }

    public function install($product)
    {
        $this->storeImages($product->getSku());

        // store images for associated simple products
        $variationsMatrix = $product->getData('variations_matrix');
        if (is_array($variationsMatrix)) {
            foreach ($variationsMatrix as $variation) {
                $this->storeImages($variation['sku']);
            }
        }
    }

    protected function storeImages($productSku)
    {
        $files = $this->getFilesBySku($productSku);
        foreach ($files as $type => $file) {
            static $i = 0; $i++;
            $types = ('main' == $type) ? ['image', 'small_image', 'thumbnail'] : ['image'];
            $galleryEntry = $this->galleryEntryBuilder->populateWithArray([
                'id' => null,
                'label' => ucfirst($type) . ' Image',
                'position' => $i,
                'types' => $types,
                'disabled' => false,
            ])->create();
            $galleryEntryContent = $this->galleryEntryContentBuilder->populateWithArray([
                'data' => base64_encode(file_get_contents($file['full'])),
                'mime_type' => 'image/jpeg',
                'name' => substr(basename($file['relative']), 0, strrpos(basename($file['relative']), '.')),
            ])->create();
            $this->writeService->create($productSku, $galleryEntry, $galleryEntryContent);
        }
    }

    protected function getColorByProductSku($productSku)
    {
        $skuSlices = explode('-', $productSku);
        $endSlice = end($skuSlices);
        $color = isset($this->colorMap[$endSlice]) ? $this->colorMap[$endSlice] : $endSlice;
        return $color;
    }

    protected function getFilesBySku($productSku)
    {
        $color = $this->getColorByProductSku($productSku);
        $colorFilesPair = $this->getColorFilesPair($productSku);
        if (isset($colorFilesPair[$color])) {
            return $colorFilesPair[$color];
        }
        return [];
    }

    protected function getColorFilesPair($sku)
    {
        $colorFilesPair = [];
        $skuParts = explode('-', strtolower($sku));
        $files = glob($this->getImagesLocationDir() . $skuParts[0] . '-*');
        if ($files) {
            foreach ($files as $index => $file) {
                $fileName = basename($file);
                if (preg_match('/^([A-Za-z0-9]+)-([A-Za-z]+)_([a-z0-9]+)(_|\.)/', $fileName, $matches)) {
                    $colorMarker = strtoupper($matches[2]);
                    $imageType = $matches[3];
                    if (isset($this->colorMap[$colorMarker])) {
                        $colorFilesPair[$this->colorMap[$colorMarker]][$imageType]['full'] = $file;
                        $colorFilesPair[$this->colorMap[$colorMarker]][$imageType]['relative'] = '/sample_data/' . $fileName;
                    }
                }
            }
        }
        return $colorFilesPair;
    }

    protected function getImagesLocationDir()
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(Filesystem::MEDIA_DIR);
        return $mediaDirectory->getAbsolutePath('sample_data/');
    }
}
