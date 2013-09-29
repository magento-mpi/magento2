<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Block\Sales\Order;

class Total extends \Magento\Core\Block\Template
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return \Magento\Reward\Block\Sales\Order\Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseRewardCurrencyAmount()) {
            $source = $this->getSource();
            $value  = - $source->getRewardCurrencyAmount();

            $this->getParentBlock()->addTotal(new \Magento\Object(array(
                'code'   => 'reward_points',
                'strong' => false,
                'label'  => $this->_rewardData->formatReward($source->getRewardPointsBalance()),
                'value'  => $source instanceof \Magento\Sales\Model\Order\Creditmemo  ? - $value : $value
            )));
        }

        return $this;
    }
}
