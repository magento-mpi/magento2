<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Block_Sales_Order_Total extends Magento_Core_Block_Template
{
    /**
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
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
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Magento_Sales_Model_Order
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
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseRewardCurrencyAmount()) {
            $source = $this->getSource();
            $value  = - $source->getRewardCurrencyAmount();

            $this->getParentBlock()->addTotal(new Magento_Object(array(
                'code'   => 'reward_points',
                'strong' => false,
                'label'  => $this->_rewardData->formatReward($source->getRewardPointsBalance()),
                'value'  => $source instanceof Magento_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }

        return $this;
    }
}
