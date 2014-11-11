<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Service\V1\Data\Product;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

class TierPriceService implements TierPriceServiceInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Product\TierPriceBuilder
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
     * @param ProductRepositoryInterface $productRepository
     * @param Product\TierPriceBuilder $priceBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Product\TierPriceBuilder $priceBuilder,
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
    public function set($productSku, $customerGroupId, \Magento\Catalog\Service\V1\Data\Product\TierPrice $price)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);

        $tierPrices = $product->getData('tier_price');
        $websiteId = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $found = false;

        foreach ($tierPrices as &$item) {
            if ('all' == $customerGroupId) {
                $isGroupValid = ($item['all_groups'] == 1);
            } else {
                $isGroupValid = ($item['cust_group'] == $customerGroupId);
            }

            if ($isGroupValid && $item['website_id'] == $websiteId && $item['price_qty'] == $price->getQty()) {
                $item['price'] = $price->getValue();
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
                'price' => $price->getValue(),
                'website_price' => $price->getValue(),
                'website_id' => $websiteId,
                'price_qty' => $price->getQty()
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
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
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
                        Product\TierPrice::VALUE => $price[$priceKey],
                        Product\TierPrice::QTY => $price['price_qty']
                    )
                );
                $prices[] = $this->priceBuilder->create();
            }
        }
        return $prices;
    }
}
