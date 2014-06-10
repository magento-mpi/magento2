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
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Product;

class GroupPriceService implements GroupPriceServiceInterface
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
     * @var \Magento\Catalog\Service\V1\Data\Product\GroupPriceBuilder
     */
    protected $groupPriceBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $customerGroupService;

    /**
     * @var \Magento\Catalog\Model\Product\PriceModifier
     */
    protected $priceModifier;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param Product\GroupPriceBuilder $groupPriceBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     * @param \Magento\Catalog\Model\Product\PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        Product\GroupPriceBuilder $groupPriceBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService,
        \Magento\Catalog\Model\Product\PriceModifier $priceModifier,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->groupPriceBuilder = $groupPriceBuilder;
        $this->storeManager = $storeManager;
        $this->customerGroupService = $customerGroupService;
        $this->priceModifier = $priceModifier;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function set($productSku, \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price)
    {
        try {
            $product = $this->productRepository->get($productSku);
            $customerGroup = $this->customerGroupService->getGroup($price->getCustomerGroupId());
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }

        $groupPrices = $product->getData('group_price');
        $websiteId = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $found = false;
        foreach ($groupPrices as &$currentPrice) {
            if ($currentPrice['cust_group'] === $price->getCustomerGroupId()
                && $currentPrice['website_id'] === $websiteId
            ) {
                $currentPrice['price'] = $price->getValue();
                $currentPrice['website_price'] = $price->getValue();
                $found = true;
                break;
            }
        }
        if (!$found) {
            $groupPrices[] = array(
                'cust_group' => $customerGroup->getId(),
                'price' => $price->getValue(),
                'website_price' => $price->getValue(),
                'website_id' => $websiteId,
            );
        }

        $product->setData('group_price', $groupPrices);
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
    public function delete($productSku, $customerGroupId)
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
        $this->priceModifier->removeGroupPrice($product, $customerGroupId, $websiteId);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        try {
            $product = $this->productRepository->get($productSku);
        } catch (\Exception $e) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }
        $priceKey = 'website_price';
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) == 0) {
            $priceKey = 'price';
        }

            $prices = array();
        foreach ($product->getData('group_price') as $price) {
            $this->groupPriceBuilder->populateWithArray(array(
                Product\GroupPrice::CUSTOMER_GROUP_ID => $price['all_groups'] ? 'all' : $price['cust_group'],
                Product\GroupPrice::VALUE => $price[$priceKey],
            ));
            $prices[] = $this->groupPriceBuilder->create();
        }
        return $prices;
    }
}
