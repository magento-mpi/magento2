<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

class GroupPriceManagement implements \Magento\Catalog\Api\ProductGroupPriceManagementInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $groupPriceBuilder;

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
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\Data\ProductGroupPriceDataBuilder $groupPriceBuilder
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     * @param PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductGroupPriceDataBuilder $groupPriceBuilder,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService,
        \Magento\Catalog\Model\Product\PriceModifier $priceModifier,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->groupPriceBuilder = $groupPriceBuilder;
        $this->customerGroupService = $customerGroupService;
        $this->priceModifier = $priceModifier;
        $this->scopeConfig = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, $customerGroupId, $price)
    {
        if ($price <= 0) {
            throw new InputException('Please provide valid data');
        }
        $customerGroup = $this->customerGroupService->getGroup($customerGroupId);
        $product = $this->productRepository->get($productSku, true);
        $groupPrices = $product->getData('group_price');
        $websiteIdentifier = 0;
        if ($this->scopeConfig->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $this->storeManager->getWebsite()->getId();
        }
        $found = false;
        foreach ($groupPrices as &$currentPrice) {
            if (intval($currentPrice['cust_group']) === $customerGroupId
                && intval($currentPrice['website_id']) === intval($websiteIdentifier)
            ) {
                $currentPrice['price'] = $price;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $groupPrices[] = array(
                'cust_group' => $customerGroup->getId(),
                'website_id' => $websiteIdentifier,
                'price' => $price,
            );
        }

        $product->setData('group_price', $groupPrices);
        $errors = $product->validate();
        if (is_array($errors) && count($errors)) {
            $errorAttributeCodes = implode(', ', array_keys($errors));
            throw new InputException(
                sprintf('Values of following attributes are invalid: %s', $errorAttributeCodes)
            );
        }
        try {
            $this->productRepository->save($product);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save group price');
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $customerGroupId)
    {
        $product = $this->productRepository->get($productSku, true);
        $websiteIdentifier = 0;
        if ($this->scopeConfig->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $this->storeManager->getWebsite()->getId();
        }
        $this->priceModifier->removeGroupPrice($product, $customerGroupId, $websiteIdentifier);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku, $websiteId = null)
    {
        $product = $this->productRepository->get($productSku, true);
        $priceKey = 'website_price';
        if ($this->scopeConfig->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) == 0) {
            $priceKey = 'price';
        }

        $prices = array();
        foreach ($product->getData('group_price') as $price) {
            $this->groupPriceBuilder->populateWithArray(array(
                'customer_group_id' => $price['all_groups'] ? 'all' : $price['cust_group'],
                'value' => $price[$priceKey],
            ));
            $prices[] = $this->groupPriceBuilder->create();
        }
        return $prices;
    }
}
