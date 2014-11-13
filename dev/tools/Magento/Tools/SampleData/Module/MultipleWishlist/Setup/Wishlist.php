<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\MultipleWishlist\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

/**
 * Installation of sample data for multiple wishlist
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
     * @var \Magento\Tools\SampleData\Module\Wishlist\Setup\Wishlist\Helper
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $wishlistColFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Tools\SampleData\Module\Wishlist\Setup\Wishlist\Helper $wishlistHelper
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Tools\SampleData\Module\Wishlist\Setup\Wishlist\Helper $wishlistHelper,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistEditor = $wishlistEditor;
        $this->wishlistColFactory = $wishlistColFactory;
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo 'Installing multiple wishlists' . PHP_EOL;
        $multipleEnabledConfig = 'wishlist/general/multiple_enabled';
        if (!$this->config->isSetFlag($multipleEnabledConfig)) {
            $this->configWriter->save($multipleEnabledConfig, 1);
            $this->configCacheType->clean();
        }

        $fixtureFiles = ['Wishlist/wishlist.csv', 'MultipleWishlist/wishlist.csv'];
        foreach ($fixtureFiles as $fixtureFile) {
            $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $csvReader = $this->csvReaderFactory->create(array('fileName' => $fixtureFilePath, 'mode' => 'r'));
            foreach ($csvReader as $row) {
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->wishlistHelper->getCustomerByEmail($row['customer_email']);
                if (!$customer) {
                    continue;
                }

                $wishlistName = $row['name'];
                /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistCollection */
                $wishlistCollection = $this->wishlistColFactory->create();
                $wishlistCollection->filterByCustomerId($customer->getId())->addFieldToFilter('name', $wishlistName);
                /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
                $wishlist = $wishlistCollection->fetchItem();
                if (!$wishlist) {
                    $wishlist = $this->wishlistEditor->edit($customer->getId(), $wishlistName, true);
                }
                if (!$wishlist->getId()) {
                    continue;
                }
                $productSkuList = explode("\n", $row['product_list']);
                $this->wishlistHelper->addProductsToWishlist($wishlist, $productSkuList);
                echo '.';
            }
        }
        echo "\n";
    }
}
