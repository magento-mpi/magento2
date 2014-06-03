<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class GroupPriceService implements GroupPriceServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function create($productSku, $customerGroupId, $price, $qty = null)
    {

    }

    public function delete($productSku, $customerGroupId, $qty = null)
    {

    }

    public function getList($productSku)
    {
        $product = $this->productFactory->create();
        $product->loadByAttribute('sku', $productSku);
        if (!$product->getId()) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }

    }

    public function get($productSku, $customerGroupId, $qty = null)
    {
        $product = $this->productFactory->create();
        $product->load($productSku, 'sku');
        if (!$product->getId()) {
            throw new NoSuchEntityException("Such product doesn't exist");
        }
        $attributeCode = $qty ? 'group_price' : 'tier_price';
        $groupPrices = $product->getData($attributeCode);

        if (!is_array($groupPrices)) {
            return array();
        }

        $result = array();

        foreach ($groupPrices as $tierPrice) {
            $row = array();
            $row['customer_group_id'] = (empty($tierPrice['all_groups']) ? $tierPrice['cust_group'] : 'all' );
            $row['website']           = ($tierPrice['website_id'] ?
                Mage::app()->getWebsite($tierPrice['website_id'])->getCode() :
                'all'
            );
            $row['qty']               = $tierPrice['price_qty'];
            $row['price']             = $tierPrice['price'];

            $result[] = $row;
        }

        return $result;
    }
}
