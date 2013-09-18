<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate model
 *
 * @method Magento_Reward_Model_Resource_Reward_Rate _getResource()
 * @method Magento_Reward_Model_Resource_Reward_Rate getResource()
 * @method int getWebsiteId()
 * @method Magento_Reward_Model_Reward_Rate setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method Magento_Reward_Model_Reward_Rate setCustomerGroupId(int $value)
 * @method int getDirection()
 * @method Magento_Reward_Model_Reward_Rate setDirection(int $value)
 * @method Magento_Reward_Model_Reward_Rate setPoints(int $value)
 * @method Magento_Reward_Model_Reward_Rate setCurrencyAmount(float $value)
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Reward_Rate extends Magento_Core_Model_Abstract
{
    const RATE_EXCHANGE_DIRECTION_TO_CURRENCY = 1;
    const RATE_EXCHANGE_DIRECTION_TO_POINTS   = 2;

    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Locale
     */
    protected $_locale;

    /**
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Reward_Model_Resource_Reward_Rate $resource
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Locale $locale
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Reward_Model_Resource_Reward_Rate $resource,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Locale $locale,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Rate text getter
     *
     * @param int $direction
     * @param int $points
     * @param float $amount
     * @param string $currencyCode
     * @return string|null
     */
    public function getRateText($direction, $points, $amount, $currencyCode = null)
    {
        switch ($direction) {
            case self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY:
                return $this->_rewardData->formatRateToCurrency($points, $amount, $currencyCode);
            case self::RATE_EXCHANGE_DIRECTION_TO_POINTS:
                return $this->_rewardData->formatRateToPoints($points, $amount, $currencyCode);
            default;
                return null;
        }
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Reward_Model_Resource_Reward_Rate');
    }

    /**
     * Processing object before save data.
     * Prepare rate data
     *
     * @return Magento_Reward_Model_Reward_Rate
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_prepareRateValues();
        return $this;
    }

    /**
     * Validate rate data
     *
     * @return boolean | string
     */
    public function validate()
    {
        return true;
    }

    /**
     * Reset rate data
     *
     * @return Magento_Reward_Model_Reward_Rate
     */
    public function reset()
    {
        $this->setData(array());
        return $this;
    }

    /**
     * Check if given rate data (website, customer group, direction)
     * is unique to current (already loaded) rate
     *
     * @param integer $websiteId
     * @param integer $customerGroupId
     * @param integer $direction
     * @return boolean
     */
    public function getIsRateUniqueToCurrent($websiteId, $customerGroupId, $direction)
    {
        $data = $this->_getResource()->getRateData($websiteId, $customerGroupId, $direction);
        if ($data && $data['rate_id'] != $this->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Prepare values in order to defined direction
     *
     * @return Magento_Reward_Model_Reward_Rate
     */
    protected function _prepareRateValues()
    {
        if ($this->_getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
            $this->setData('points', (int)$this->_getData('value'));
            $this->setData('currency_amount', (float)$this->_getData('equal_value'));
        } elseif ($this->_getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_POINTS) {
            $this->setData('currency_amount', (float)$this->_getData('value'));
            $this->setData('points', (int)$this->_getData('equal_value'));
        }
        return $this;
    }

    /**
     * Fetch rate by customer group and website
     *
     * @param integer $customerGroupId
     * @param integer $websiteId
     * @return Magento_Reward_Model_Reward_Rate
     */
    public function fetch($customerGroupId, $websiteId, $direction) {
        $this->setData('original_website_id', $websiteId)
            ->setData('original_customer_group_id', $customerGroupId);
        $this->_getResource()->fetch($this, $customerGroupId, $websiteId, $direction);
        return $this;
    }

    /**
     * Calculate currency amount of given points by rate
     *
     * @param integer $points
     * @param bool Whether to round points to integer or not
     * @return float
     */
    public function calculateToCurrency($points, $rounded = true)
    {
        $amount = 0;
        if ($this->getPoints()) {
            if ($rounded) {
                $roundedPoints = (int)($points/$this->getPoints());
            } else {
                $roundedPoints = round($points/$this->getPoints(), 2);
            }
            if ($roundedPoints) {
                $amount = $this->getCurrencyAmount()*$roundedPoints;
            }
        }
        return (float)$amount;
    }

    /**
     * Calculate points of given amount by rate
     *
     * @param float $amount
     * @return integer
     */
    public function calculateToPoints($amount)
    {
        $points = 0;
        if ($this->getCurrencyAmount() && $amount >= $this->getCurrencyAmount()) {
            /**
             * Type casting made in such way to avoid wrong automatic type casting and calculation.
             * $amount always int and $this->getCurrencyAmount() is string or float
             */
            $amountValue = (int)((string)$amount/(string)$this->getCurrencyAmount());
            if ($amountValue) {
                $points = $this->getPoints()*$amountValue;
            }
        }
        return $points;
    }

    /**
     * Retrieve option array of rate directions with labels
     *
     * @return array
     */
    public function getDirectionsOptionArray()
    {
        $optArray = array(
            self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY => __('Points to Currency'),
            self::RATE_EXCHANGE_DIRECTION_TO_POINTS => __('Currency to Points')
        );
        return $optArray;
    }

    /**
     * Getter for currency part of the rate
     * Formatted value returns string
     *
     * @param bool $formatted
     * @return mixed|string
     */
    public function getCurrencyAmount($formatted = false)
    {
        $amount = $this->_getData('currency_amount');
        if ($formatted) {
            $websiteId = $this->getOriginalWebsiteId();
            if ($websiteId === null) {
                $websiteId = $this->getWebsiteId();
            }
            $currencyCode = $this->_storeManager->getWebsite($websiteId)->getBaseCurrencyCode();
            return $this->_locale->currency($currencyCode)->toCurrency($amount);
        }
        return $amount;
    }

    /**
     * Getter for points part of the rate
     * Formatted value returns as int
     *
     * @param bool $formatted
     * @return mixed|int
     */
    public function getPoints($formatted = false)
    {
        $pts = $this->_getData('points');
        return $formatted ? (int)$pts : $pts;
    }
}
