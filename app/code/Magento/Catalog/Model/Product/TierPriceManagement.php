<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

class TierPriceManagement implements \Magento\Catalog\Api\ProductTierPriceManagementInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductTierPriceDataBuilder
     */
    protected $priceBuilder;

    /**
     * @var \Magento\Framework\StoreManagerInterface
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
     * @param ProductRepository $productRepository
     * @param \Magento\Catalog\Api\Data\ProductTierPriceDataBuilder $priceBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     */
    public function __construct(
        ProductRepository $productRepository,
        \Magento\Catalog\Api\Data\ProductTierPriceDataBuilder $priceBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\PriceModifier $priceModifier,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->productRepository = $productRepository;
        $this->priceBuilder = $priceBuilder;
        $this->storeManager = $storeManager;
        $this->priceModifier = $priceModifier;
        $this->config = $config;
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function add($productSku, $customerGroupId, $price, $qty)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);

        $tierPrices = $product->getData('tier_price');
        $websiteIdentifier = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $this->storeManager->getWebsite()->getId();
        }
        $found = false;

        foreach ($tierPrices as &$item) {
            if ('all' == $customerGroupId) {
                $isGroupValid = ($item['all_groups'] == 1);
            } else {
                $isGroupValid = ($item['cust_group'] == $customerGroupId);
            }

            if ($isGroupValid && $item['website_id'] == $websiteIdentifier && $item['price_qty'] == $qty) {
                $item['price'] = $price;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $mappedCustomerGroupId = 'all' == $customerGroupId
                ? \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL
                : $this->customerGroupService->getGroup($customerGroupId)->getId();

            $tierPrices[] = array(
                'cust_group' => $mappedCustomerGroupId,
                'price' => $price,
                'website_price' => $price,
                'website_id' => $websiteIdentifier,
                'price_qty' => $qty
            );
        }

        $product->setData('tier_price', $tierPrices);
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
    public function remove($productSku, $customerGroupId, $qty)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
        $websiteIdentifier = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $this->storeManager->getWebsite()->getId();
        }
        $this->priceModifier->removeTierPrice($product, $customerGroupId, $qty, $websiteIdentifier);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku, $customerGroupId)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);

        $priceKey = 'website_price';
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) == 0) {
            $priceKey = 'price';
        }

        $prices = array();
        foreach ($product->getData('tier_price') as $price) {
            if ((is_numeric($customerGroupId) && intval($price['cust_group']) === intval($customerGroupId))
                || ($customerGroupId === 'all' && $price['all_groups'])
            ) {
                $this->priceBuilder->populateWithArray(
                    array(
                        \Magento\Catalog\Api\Data\ProductTierPriceInterface::VALUE => $price[$priceKey],
                        \Magento\Catalog\Api\Data\ProductTierPriceInterface::QTY => $price['price_qty']
                    )
                );
                $prices[] = $this->priceBuilder->create();
            }
        }
        return $prices;
    }
}
