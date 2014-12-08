<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Customer newsletter subscription
 */
class Newsletter extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Customer\Newsletter');
        $this->setValue(1);
    }

    /**
     * Set data with filtering
     *
     * @param array|string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (int)$key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (int)$value;
        }

        return parent::setData($key, $value);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return ['customer_save_commit_after', 'newsletter_subscriber_save_commit_after'];
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [['value' => $this->getType(), 'label' => __('Newsletter Subscription')]];
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml() . __(
            'Customer is %1 to newsletter.',
            $element
        ) . $this->getRemoveLinkHtml();
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
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(['1' => __('subscribed'), '0' => __('not subscribed')]);
        return $this;
    }

    /**
     * Get condition query for customer balance
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('newsletter_subscriber');
        $value = (int)$this->getValue();

        $select = $this->getResource()->createSelect()->from(
            ['main' => $table],
            [new \Zend_Db_Expr($value)]
        )->where(
            $this->_createCustomerFilter($customer, 'main.customer_id')
        )->where(
            'main.subscriber_status = ?',
            \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
        );
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'main.store_id');
        if (!$value) {
            $select = $this->getResource()->getReadConnection()->getIfNullSql($select, 1);
        }
        return $select;
    }
}
