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
namespace Magento\Reward\Model\Action;

class OrderExtra extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Quote instance, required for estimating checkout reward (order subtotal - discount)
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote = null;

    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param array $data
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($data);
    }

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
     * @return \Magento\Reward\Model\Action\AbstractAction
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
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Reward\Model\Action\OrderExtra
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
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
        if (!$this->_rewardData->isOrderAllowed($this->getReward()->getWebsiteId())) {
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
            && $this->_rewardData->isOrderAllowed($this->getReward()->getWebsiteId());
    }
}
