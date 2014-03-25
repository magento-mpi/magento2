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
class GroupPrice extends Price implements OriginPrice
{
    /**
     * @var string
     */
    protected $priceType = 'group_price';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param SaleableInterface $salableItem
     * @param Session $customerSession
     * @param int $quantity
     */
    public function __construct(SaleableInterface $salableItem, Session $customerSession, $quantity)
    {
        $this->customerSession = $customerSession;
        parent::__construct($salableItem, $quantity);
    }

    /**
     * @return float|bool
     */
    public function getValue()
    {
        $groupPrices = $this->salableItem->getData('group_price');
        $matchedPrice = false;

        if (null === $groupPrices) {
            $attribute = $this->salableItem->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($this->salableItem);
                $groupPrices = $this->salableItem->getData('group_price');
            }
        }

        if (null === $groupPrices || !is_array($groupPrices)) {
            return $matchedPrice;
        }

        $customerGroup = $this->getCustomerGroupId();

        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }
        return $matchedPrice;
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
}
