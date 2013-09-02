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
 * Advertising Tooltip block to show different messages for gaining reward points
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Tooltip extends Magento_Core_Block_Template
{
    /**
     * Reward instance
     *
     * @var Magento_Reward_Model_Reward
     */
    protected $_rewardInstance = null;

    /**
     * Reward action instance
     *
     * @var Magento_Reward_Model_Action_Abstract
     */
    protected $_actionInstance = null;

    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($coreData, $context, $data);
    }

    public function initRewardType($action)
    {
        if ($action) {
            if (!$this->_rewardData->isEnabledOnFront()) {
                return $this;
            }
            $customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
            $this->_rewardInstance = Mage::getSingleton('Magento_Reward_Model_Reward')
                ->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->_actionInstance = $this->_rewardInstance->getActionInstance($action, true);
        }
    }

    /**
     * Getter for amount customer may be rewarded for current action
     * Can format as currency
     *
     * @param float $amount
     * @param bool $asCurrency
     * @return string|null
     */
    public function getRewardAmount($amount = null, $asCurrency = false)
    {
        $amount = null === $amount ? $this->_getData('reward_amount') : $amount;
        return $this->_rewardData->formatAmount($amount, $asCurrency);
    }

    public function renderLearnMoreLink($format = '<a href="%1$s">%2$s</a>', $anchorText = null)
    {
        $anchorText = null === $anchorText ? __('Learn more...') : $anchorText;
        return sprintf($format, $this->getLandingPageUrl(), $anchorText);
    }

    /**
     * Set various template variables
     */
    protected function _prepareTemplateData()
    {
        if ($this->_actionInstance) {
            $this->addData(array(
                'reward_points' => $this->_rewardInstance->estimateRewardPoints($this->_actionInstance),
                'landing_page_url' => $this->_rewardData->getLandingPageUrl(),
            ));

            if ($this->_rewardInstance->getId()) {
                // estimate qty limitations (actually can be used without customer reward record)
                $qtyLimit = $this->_actionInstance->estimateRewardsQtyLimit();
                if (null !== $qtyLimit) {
                    $this->setData('qty_limit', $qtyLimit);
                }

                if ($this->hasGuestNote()) {
                    $this->unsGuestNote();
                }

                $this->addData(array(
                    'points_balance' => $this->_rewardInstance->getPointsBalance(),
                    'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),
                ));
                // estimate monetary reward
                $amount = $this->_rewardInstance->estimateRewardAmount($this->_actionInstance);
                if (null !== $amount) {
                    $this->setData('reward_amount', $amount);
                }
            } else {
                if ($this->hasIsGuestNote() && !$this->hasGuestNote()) {
                    $this->setGuestNote(__('This applies only to registered users and may vary when a user is logged in.'));
                }
            }
        }
    }

    /**
     * Check whether everything is set for output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_prepareTemplateData();
        if (!$this->_actionInstance || !$this->getRewardPoints() || $this->hasQtyLimit() && !$this->getQtyLimit()) {
            return '';
        }
        return parent::_toHtml();
    }
}
