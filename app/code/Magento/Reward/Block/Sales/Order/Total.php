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

class Total extends \Magento\View\Element\Template
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardData,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($context, $data);
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
     * @return $this
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
