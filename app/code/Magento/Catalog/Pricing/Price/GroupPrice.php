<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use \Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;

/**
 * Group price model
 */
class GroupPrice extends AbstractPrice implements GroupPriceInterface, BasePriceProviderInterface
{
    /**
     * Price type group
     */
    const PRICE_CODE = 'group_price';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var array|null
     */
    protected $storedGroupPrice;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param Session $customerSession
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        Session $customerSession
    ) {
        parent::__construct($saleableItem, $quantity, $calculator);
        $this->customerSession = $customerSession;
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->value = false;
            $customerGroup = $this->getCustomerGroupId();
            foreach ($this->getStoredGroupPrice() as $groupPrice) {
                if ($groupPrice['cust_group'] == $customerGroup) {
                    $this->value = (float) $groupPrice['website_price'];
                    break;
                }
            }
        }
        return $this->value;
    }

    /**
     * @return int
     */
    protected function getCustomerGroupId()
    {
        if ($this->product->getCustomerGroupId()) {
            return (int) $this->product->getCustomerGroupId();
        }
        return (int) $this->customerSession->getCustomerGroupId();
    }

    /**
     * @return array
     */
    public function getStoredGroupPrice()
    {
        if (null === $this->storedGroupPrice) {
            $resource = $this->product->getResource();
            $attribute =  $resource->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($this->product);
                $this->storedGroupPrice = $this->product->getData('group_price');
            }
            if (null === $this->storedGroupPrice || !is_array($this->storedGroupPrice)) {
                $this->storedGroupPrice = [];
            }
        }
        return $this->storedGroupPrice;
    }
}
