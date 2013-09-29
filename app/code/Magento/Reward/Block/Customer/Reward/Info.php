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
 * Customer account reward points balance block
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Customer\Reward;

class Info extends \Magento\Core\Block\Template
{
    /**
     * Reward pts model instance
     *
     * @var \Magento\Reward\Model\Reward
     */
    protected $_rewardInstance = null;

    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_rewardFactory = $rewardFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Render if all there is a customer and a balance
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customer = $this->_customerSession->getCustomer();
        if ($customer && $customer->getId()) {
            $this->_rewardInstance = $this->_rewardFactory->create()
                ->setCustomer($customer)
                ->setWebsiteId($this->_storeManager->getWebsite()->getId())
                ->loadByCustomer();
            if ($this->_rewardInstance->getId()) {
                $this->_prepareTemplateData();
                return parent::_toHtml();
            }
        }
        return '';
    }

    /**
     * Set various variables requested by template
     */
    protected function _prepareTemplateData()
    {
        $helper = $this->_rewardData;
        $maxBalance = (int)$helper->getGeneralConfig('max_points_balance');
        $minBalance = (int)$helper->getGeneralConfig('min_points_balance');
        $balance = $this->_rewardInstance->getPointsBalance();
        $this->addData(array(
            'points_balance' => $balance,
            'currency_balance' => $this->_rewardInstance->getCurrencyAmount(),
            'pts_to_amount_rate_pts' => $this->_rewardInstance->getRateToCurrency()->getPoints(true),
            'pts_to_amount_rate_amount' => $this->_rewardInstance->getRateToCurrency()->getCurrencyAmount(),
            'amount_to_pts_rate_amount' => $this->_rewardInstance->getRateToPoints()->getCurrencyAmount(),
            'amount_to_pts_rate_pts' => $this->_rewardInstance->getRateToPoints()->getPoints(true),
            'max_balance' => $maxBalance,
            'is_max_balance_reached' => $balance >= $maxBalance,
            'min_balance' => $minBalance,
            'is_min_balance_reached' => $balance >= $minBalance,
            'expire_in' => (int)$helper->getGeneralConfig('expiration_days'),
            'is_history_published' => (int)$helper->getGeneralConfig('publish_history'),
        ));
    }
}
