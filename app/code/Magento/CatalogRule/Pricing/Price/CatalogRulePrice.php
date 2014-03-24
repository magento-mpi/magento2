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

/**
 * Class CatalogRulePrice
 */
class CatalogRulePrice extends \Magento\Catalog\Pricing\Price\Price
{
    /**
     * Price type identifier string
     */
    const PRICE_CATALOG_RULE = 'catalog_rule_price';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_CATALOG_RULE;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var \Magento\Core\Model\StoreManager
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
     * @param \Magento\Pricing\Object\SaleableInterface $salableItem
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $dateTime
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogRule\Model\Resource\RuleFactory $catalogRuleResourceFactory
     * @param int $quantity
     */
    public function __construct(
        \Magento\Pricing\Object\SaleableInterface $salableItem,
        \Magento\Stdlib\DateTime\TimezoneInterface $dateTime,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogRule\Model\Resource\RuleFactory $catalogRuleResourceFactory,
        $quantity = 1
    ) {
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->resourceRuleFactory = $catalogRuleResourceFactory;
        parent::__construct($salableItem, $quantity);
    }

    /**
     * Returns catalog rule value
     *
     * @return float|null
     */
    public function getValue()
    {
        if (!$this->baseAmount) {
            $this->baseAmount = $this->resourceRuleFactory->create()
                ->getRulePrice(
                    $this->dateTime->scopeTimeStamp($this->storeManager->getCurrentStore()->getId()),
                    $this->storeManager->getCurrentStore()->getWebsiteId(),
                    $this->customerSession->getCustomerGroupId(),
                    $this->salableItem->getId()
                );
        }
        return is_null($this->baseAmount) ? null : floatval($this->baseAmount);
    }
}
