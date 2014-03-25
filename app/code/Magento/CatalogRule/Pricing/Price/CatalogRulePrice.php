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

use Magento\Catalog\Pricing\Price\Price;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\PriceInfo\Base;
use Magento\Stdlib\DateTime\TimezoneInterface;
use Magento\Core\Model\StoreManager;
use Magento\Customer\Model\Session;
use Magento\CatalogRule\Model\Resource\RuleFactory;

/**
 * Class CatalogRulePrice
 */
class CatalogRulePrice extends Price
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
     * @param SaleableInterface $salableItem
     * @param TimezoneInterface $dateTime
     * @param StoreManager $storeManager
     * @param Session $customerSession
     * @param RuleFactory $catalogRuleResourceFactory
     * @param float $quantity
     */
    public function __construct(
        SaleableInterface $salableItem,
        TimezoneInterface $dateTime,
        StoreManager $storeManager,
        Session $customerSession,
        RuleFactory $catalogRuleResourceFactory,
        $quantity = Base::PRODUCT_QUANTITY_DEFAULT
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
     * @return float|boolean
     */
    public function getValue()
    {
        if (!$this->baseAmount) {
            $this->baseAmount = $this->resourceRuleFactory->create()
                ->getRulePrice(
                    $this->dateTime->scopeTimeStamp($this->storeManager->getStore()->getId()),
                    $this->storeManager->getStore()->getWebsiteId(),
                    $this->customerSession->getCustomerGroupId(),
                    $this->salableItem->getId()
                );
        }
        return $this->baseAmount ? floatval($this->baseAmount) : false;
    }
}
