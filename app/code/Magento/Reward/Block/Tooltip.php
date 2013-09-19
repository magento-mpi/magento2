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
namespace Magento\Reward\Block;

class Tooltip extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardHelper;

    /**
     * Reward instance
     *
     * @var \Magento\Reward\Model\Reward
     */
    protected $_rewardInstance;

    /**
     * Reward action instance
     *
     * @var \Magento\Reward\Model\Action\AbstractAction
     */
    protected $_actionInstance = null;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Reward\Model\Reward $rewardInstance
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Reward\Model\Reward $rewardInstance,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $customerSession;
        $this->_rewardHelper = $rewardHelper;
        $this->_rewardInstance = $rewardInstance;
        $this->_storeManager = $storeManager;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($action = $this->getRewardType()) {
            if (!$this->_rewardHelper->isEnabledOnFront()) {
                return $this;
            }
            $this->_rewardInstance
                ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
                ->setCustomer($this->_customerSession->getCustomer())
                ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
                ->loadByCustomer();
            $this->_actionInstance = $this->_rewardInstance->getActionInstance($action, true);
        }
        return $this;
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
        return $this->_rewardHelper->formatAmount($amount, $asCurrency);
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
                'landing_page_url' => $this->_rewardHelper->getLandingPageUrl(),
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
