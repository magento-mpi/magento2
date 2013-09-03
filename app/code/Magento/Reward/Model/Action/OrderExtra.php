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
 * Reward action for converting spent money to points
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Action_OrderExtra extends Magento_Reward_Model_Action_Abstract
{
    /**
     * Quote instance, required for estimating checkout reward (order subtotal - discount)
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return __('Earned points for order #%1', $incrementId);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param \Magento\Object $entity
     * @return Magento_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array(
            'increment_id' => $this->getEntity()->getIncrementId()
        ));
        return $this;
    }

    /**
     * Quote setter
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return Magento_Reward_Model_Action_OrderExtra
     */
    public function setQuote(Magento_Sales_Model_Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        if (!Mage::helper('Magento_Reward_Helper_Data')->isOrderAllowed($this->getReward()->getWebsiteId())) {
            return 0;
        }
        if ($this->_quote) {
            $quote = $this->_quote;
            // known issue: no support for multishipping quote
            $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            // use only money customer spend - shipping & tax
            $monetaryAmount = $quote->getBaseGrandTotal()
                - $address->getBaseShippingAmount()
                - $address->getBaseTaxAmount();
            $monetaryAmount = $monetaryAmount < 0 ? 0 : $monetaryAmount;
        } else {
            $monetaryAmount = $this->getEntity()->getBaseTotalPaid()
                - $this->getEntity()->getBaseShippingAmount()
                - $this->getEntity()->getBaseTaxAmount();
        }
        $pointsDelta = $this->getReward()->getRateToPoints()->calculateToPoints((float)$monetaryAmount);
        return $pointsDelta;
    }

    /**
     * Check whether rewards can be added for action
     * Checking for the history records is intentionaly omitted
     *
     * @return bool
     *
     */
    public function canAddRewardPoints()
    {
        return parent::canAddRewardPoints()
            && Mage::helper('Magento_Reward_Helper_Data')->isOrderAllowed($this->getReward()->getWebsiteId());
    }
}
