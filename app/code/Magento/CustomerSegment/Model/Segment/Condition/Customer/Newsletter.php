<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer newsletter subscription
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer;

class Newsletter
    extends \Magento\CustomerSegment\Model\Condition\AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Newsletter');
        $this->setValue(1);
    }

    /**
     * Set data with filtering
     *
     * @param mixed $key
     * @param mixed $value
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Customer\Newsletter
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (int) $key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (int) $value;
        }

        return parent::setData($key, $value);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_save_commit_after', 'newsletter_subscriber_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(array(
            'value' => $this->getType(),
            'label' => __('Newsletter Subscription')
         ));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml()
            . __('Customer is %1 to newsletter.', $element)
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return array
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1'  => __('subscribed'),
            '0' => __('not subscribed'),
        ));
        return $this;
    }

    /**
     * Get condition query for customer balance
     *
     * @param $customer
     * @param int|Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('newsletter_subscriber');
        $value = (int)$this->getValue();

        $select = $this->getResource()->createSelect()
            ->from(array('main' => $table), array(new \Zend_Db_Expr($value)))
            ->where($this->_createCustomerFilter($customer, 'main.customer_id'))
            ->where('main.subscriber_status = ?', \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED);
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'main.store_id');
        if (!$value) {
            $select = $this->getResource()->getReadConnection()->getIfNullSql($select, 1);
        }
        return $select;
    }
}
