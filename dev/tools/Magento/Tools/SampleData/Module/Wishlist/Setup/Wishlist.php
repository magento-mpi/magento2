<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Wishlist\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Installation of sample data for wishlist
 */
class Wishlist implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var Wishlist\Helper;
     */
    protected $wishlistHelper;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Wishlist\Helper $wishlistHelper
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Wishlist\Helper $wishlistHelper,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing wishlists' . PHP_EOL;

        $fixtureFile = 'Wishlist/wishlist.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
        foreach ($csvReader as $row) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->wishlistHelper->getCustomerByEmail($row['customer_email']);
            if (!$customer) {
                continue;
            }

            /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
            $wishlist = $this->wishlistFactory->create();
            $wishlist->loadByCustomerId($customer->getId(), true);
            if (!$wishlist->getId()) {
                continue;
            }
            $productSkuList = explode("\n", $row['product_list']);
            $this->wishlistHelper->addProductsToWishlist($wishlist, $productSkuList);
            echo ".";
        }
        echo "\n";
    }
}
