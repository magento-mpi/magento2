<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\GiftCard\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Setup Gift Card
 */
class Product extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param array $fixtures
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        $fixtures = array(
            'GiftCard/products_giftcard.csv'
        )
    ) {
        $gallery->setFixtures([
            'GiftCard/images_giftcard.csv'
        ]);
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
            $gallery,
            $fixtures
        );
    }
}
