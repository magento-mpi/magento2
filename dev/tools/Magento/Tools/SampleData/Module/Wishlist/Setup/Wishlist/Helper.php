<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Wishlist\Setup\Wishlist;

class Helper
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * @param string $email
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomerByEmail($email)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId(1);
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param array $productSkuList
     * @return void
     */
    public function addProductsToWishlist(\Magento\Wishlist\Model\Wishlist $wishlist, $productSkuList)
    {
        $shouldSave = false;
        foreach ($productSkuList as $productSku) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productFactory->create();
            $productId = $product->getIdBySku($productSku);
            if (empty($productId)) {
                continue;
            } elseif (!$shouldSave) {
                $shouldSave = true;
            }
            $wishlist->addNewItem($productId, null, true);
        }
        if ($shouldSave) {
            $wishlist->save();
        }
    }
}
