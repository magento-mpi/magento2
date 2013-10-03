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
 * Reward action for updating balance by salesrule
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Action;

class Salesrule extends \Magento\Reward\Model\Action\AbstractAction
{
     /**
     * Quote instance, required for estimating checkout reward (rule defined static value)
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote = null;

    /**
     * @var \Magento\Reward\Model\Resource\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param \Magento\Reward\Model\Resource\RewardFactory $rewardFactory
     * @param array $data
     */
    public function __construct(\Magento\Reward\Model\Resource\RewardFactory $rewardFactory, array $data = array())
    {
        $this->_rewardFactory = $rewardFactory;
        parent::__construct($data);
    }

    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId) {
        $pointsDelta = 0;
        if ($this->_quote) {
            // known issue: no support for multishipping quote // copied  comment, not checked
            if ($this->_quote->getAppliedRuleIds()) { 
                $ruleIds = explode(',', $this->_quote->getAppliedRuleIds());
                $ruleIds = array_unique($ruleIds);
                $data = $this->_rewardFactory->create()->getRewardSalesrule($ruleIds);
                foreach ($data as $rule) {
                    $pointsDelta += (int)$rule['points_delta'];
                }
            }
        }
        return $pointsDelta;
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
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
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
        return __('Earned promotion extra points from order #%1', $incrementId);
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
}
