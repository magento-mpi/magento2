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
 * @method \Magento\Reward\Model\Resource\Reward\Rate _getResource()
 * @method \Magento\Reward\Model\Resource\Reward\Rate getResource()
 * @method int getWebsiteId()
 * @method \Magento\Reward\Model\Reward\Rate setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method \Magento\Reward\Model\Reward\Rate setCustomerGroupId(int $value)
 * @method int getDirection()
 * @method \Magento\Reward\Model\Reward\Rate setDirection(int $value)
 * @method \Magento\Reward\Model\Reward\Rate setPoints(int $value)
 * @method \Magento\Reward\Model\Reward\Rate setCurrencyAmount(float $value)
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Reward;

class Rate extends \Magento\Core\Model\AbstractModel
{
    const RATE_EXCHANGE_DIRECTION_TO_CURRENCY = 1;
    const RATE_EXCHANGE_DIRECTION_TO_POINTS   = 2;

    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Locale
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\Resource\Reward\Rate $resource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\Resource\Reward\Rate $resource,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
        $this->_init('Magento\Reward\Model\Resource\Reward\Rate');
    }

    /**
     * Processing object before save data.
     * Prepare rate data
     *
     * @return $this
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
     * @return bool | string
     */
    public function validate()
    {
        return true;
    }

    /**
     * Reset rate data
     *
     * @return $this
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
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $direction
     * @return bool
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
     * @return $this
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
     * @param int $customerGroupId
     * @param int $websiteId
     * @param int $direction
     * @return $this
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
     * @param int $points
     * @param bool $rounded whether to round points to integer or not
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
