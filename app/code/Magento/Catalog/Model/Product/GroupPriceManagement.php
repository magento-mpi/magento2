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
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductGroupPriceBuilder
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
    protected $config;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Api\Data\ProductGroupPriceInterfaceBuilder $groupPriceBuilder
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService
     * @param PriceModifier $priceModifier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Api\Data\ProductGroupPriceInterfaceBuilder $groupPriceBuilder,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService,
        \Magento\Catalog\Model\Product\PriceModifier $priceModifier,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->productRepository = $productRepository;
        $this->groupPriceBuilder = $groupPriceBuilder;
        $this->customerGroupService = $customerGroupService;
        $this->priceModifier = $priceModifier;
        $this->config = $config;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, $customerGroupId, $price, $websiteId = null)
    {
        $customerGroup = $this->customerGroupService->getGroup($customerGroupId);
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
        $groupPrices = $product->getData('group_price');
        $websiteIdentifier = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $websiteId;
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
    public function remove($productSku, $customerGroupId, $websiteId = null)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
        $websiteIdentifier = 0;
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) != 0) {
            $websiteIdentifier = $websiteId;
        }
        $this->priceModifier->removeGroupPrice($product, $customerGroupId, $websiteIdentifier);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku, $websiteId = null)
    {
        $product = $this->productRepository->get($productSku, ['edit_mode' => true]);
        $priceKey = 'website_price';
        if ($this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) == 0) {
            $priceKey = 'price';
        }

        $prices = array();
        foreach ($product->getData('group_price') as $price) {
            if (isset($websiteId) && $price['website_id'] != $websiteId) {
                break;
            }
            $this->groupPriceBuilder->populateWithArray(array(
                'customer_group_id' => $price['all_groups'] ? 'all' : $price['cust_group'],
                'value' => $price[$priceKey],
            ));
            $prices[] = $this->groupPriceBuilder->create();
        }
        return $prices;
    }
}
