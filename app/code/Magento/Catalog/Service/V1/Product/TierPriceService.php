<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Product;


class TierPriceService implements TierPriceServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Product\TierPriceBuilder
     */
    protected $priceBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param Product\TierPriceBuilder $priceBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        Product\TierPriceBuilder $priceBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->priceBuilder = $priceBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($productSku, \Magento\Catalog\Service\V1\Data\Product\TierPrice $price)
    {
        // TODO: Implement create() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete($productSku, $customerGroupId, $qty)
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $customerGroupId, $qty)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku, $customerGroupId)
    {
        try {
            $product = $this->productRepository->get($productSku);
        } catch (\Exception $e) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }

        $prices = array();
        foreach ($product->getData('tier_price') as $price) {
            if ((is_numeric($customerGroupId) && intval($price['cust_group']) === intval($customerGroupId))
                || ($customerGroupId === 'all' && $price['all_groups'])
            ) {
                $prices[] = $this->priceBuilder->populateWithArray(array(
                    Product\TierPrice::CUSTOMER_GROUP_ID => $price['all_groups'] ? 'all' : $price['cust_group'],
                    Product\TierPrice::VALUE => $price['website_price'],
                    Product\TierPrice::QTY => $price['price_qty']
                ))->create();
            }
        }
        return $prices;
    }
}
