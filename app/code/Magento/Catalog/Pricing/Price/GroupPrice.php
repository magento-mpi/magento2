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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Customer\Model\Session;

/**
 * Group price model
 */
class GroupPrice extends Price implements GroupPriceInterface, OriginPrice
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
     * @var float|bool|null
     */
    protected $value;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Session $customerSession
     */
    public function __construct(SaleableInterface $salableItem, $quantity, Session $customerSession)
    {
        $this->customerSession = $customerSession;
        parent::__construct($salableItem, $quantity);
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        if (null === $this->value) {
            $this->value = false;
            $groupPrices = $this->getStoredGroupPrice();

            if (null === $groupPrices || !is_array($groupPrices)) {
                return $this->value;
            }

            $customerGroup = $this->getCustomerGroupId();

            foreach ($groupPrices as $groupPrice) {
                if ($groupPrice['cust_group'] == $customerGroup) {
                    $this->value = $groupPrice['website_price'];
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
     * @return array|null
     */
    protected function getStoredGroupPrice()
    {
        $groupPrices = $this->salableItem->getData('group_price');

        if (null === $groupPrices) {
            $attribute = $this->salableItem->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($this->salableItem);
                $groupPrices = $this->salableItem->getData('group_price');
            }
        }

        return $groupPrices;
    }
}
