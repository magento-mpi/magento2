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
use Magento\Framework\Exception\CouldNotSaveException;
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
     * @var \Magento\Catalog\Model\Product\PriceModifier
     */
    protected $priceModifier;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $customerGroupService;

    /**
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param Product\TierPriceBuilder $priceBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        Product\TierPriceBuilder $priceBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\PriceModifier $priceModifier,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->priceBuilder = $priceBuilder;
        $this->storeManager = $storeManager;
        $this->priceModifier = $priceModifier;
        $this->config = $config;
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * {@inheritdoc}
     */
    public function set($productSku, $customerGroupId, \Magento\Catalog\Service\V1\Data\Product\TierPrice $price)
    {
        try {
            $product = $this->productRepository->get($productSku);
            $customerGroup = $this->customerGroupService->getGroup($customerGroupId);
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }

        $tierPrices = $product->getData('tier_price');
        $websiteId = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $found = false;
        foreach ($tierPrices as &$currentPrice) {
            if ($currentPrice['cust_group'] === $customerGroupId
                && $currentPrice['website_id'] === $websiteId
            ) {
                $currentPrice['price'] = $price->getValue();
                $found = true;
                break;
            }
        }
        if (!$found) {
            $tierPrices[] = array(
                'cust_group' => $customerGroup->getId(),
                'price' => $price->getValue(),
                'website_price' => $price->getValue(),
                'website_id' => $websiteId,
                'price_qty' => $price->getQty()
            );
        }

        print_r($tierPrices);

        $product->setData('tier_price', $tierPrices);
        $errors = $product->validate();
        if (is_array($errors) && count($errors)) {
            $errorAttributeCodes = implode(', ', array_keys($errors));
            throw new CouldNotSaveException(
                sprintf('Values of following attributes are invalid: %s', $errorAttributeCodes)
            );
        }
        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save group price');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($productSku, $customerGroupId, $qty)
    {
        $product = $this->productFactory->create();
        $productId = $product->getIdBySku($productSku);

        if (!$productId) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }
        $product->load($productId);
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) == 0) {
            $websiteId = 0;
        } else {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }
        $this->priceModifier->removeTierPrice($product, $customerGroupId, $qty, $websiteId);
        return true;
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
                    Product\TierPrice::VALUE => $price['website_price'],
                    Product\TierPrice::QTY => $price['price_qty']
                ))->create();
            }
        }
        return $prices;
    }
}
