<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Customer\Model\Session;

/**
 * Group price model
 */
class GroupPrice extends RegularPrice implements GroupPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_GROUP;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var array|null
     */
    protected $storedGroupPrice;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Calculator $calculator
     * @param Session $customerSession
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        Calculator $calculator,
        Session $customerSession
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
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
        if ($this->salableItem->getCustomerGroupId()) {
            return (int) $this->salableItem->getCustomerGroupId();
        }
        return (int) $this->customerSession->getCustomerGroupId();
    }

    /**
     * @return array
     */
    public function getStoredGroupPrice()
    {
        if (null !== $this->storedGroupPrice) {
            return $this->storedGroupPrice;
        }

        $this->storedGroupPrice = $this->salableItem->getData('group_price');

        if (null === $this->storedGroupPrice) {
            $attribute = $this->salableItem->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($this->salableItem);
                $this->storedGroupPrice = $this->salableItem->getData('group_price');
            }
        }
        if (null === $this->storedGroupPrice || !is_array($this->storedGroupPrice)) {
            $this->storedGroupPrice = [];
        }
        return $this->storedGroupPrice;
    }
}
