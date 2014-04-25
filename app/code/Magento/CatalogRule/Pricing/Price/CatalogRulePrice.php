<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\Pricing\Adjustment\Calculator;
use Magento\Framework\Pricing\Object\SaleableInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManager;
use Magento\Customer\Model\Session;
use Magento\CatalogRule\Model\Resource\RuleFactory;

/**
 * Class CatalogRulePrice
 */
class CatalogRulePrice extends RegularPrice
{
    /**
     * Price type identifier string
     */
    const PRICE_TYPE = 'catalog_rule_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\CatalogRule\Model\Resource\RuleFactory
     */
    protected $resourceRuleFactory;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Calculator $calculator
     * @param TimezoneInterface $dateTime
     * @param StoreManager $storeManager
     * @param Session $customerSession
     * @param RuleFactory $catalogRuleResourceFactory
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        Calculator $calculator,
        TimezoneInterface $dateTime,
        StoreManager $storeManager,
        Session $customerSession,
        RuleFactory $catalogRuleResourceFactory
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->resourceRuleFactory = $catalogRuleResourceFactory;
    }

    /**
     * Returns catalog rule value
     *
     * @return float|boolean
     */
    public function getValue()
    {
        if (null === $this->value) {
            $this->value = $this->resourceRuleFactory->create()
                ->getRulePrice(
                    $this->dateTime->scopeTimeStamp($this->storeManager->getStore()->getId()),
                    $this->storeManager->getStore()->getWebsiteId(),
                    $this->customerSession->getCustomerGroupId(),
                    $this->salableItem->getId()
                );
            $this->value = $this->value ? floatval($this->value) : false;
        }
        return $this->value;
    }
}
