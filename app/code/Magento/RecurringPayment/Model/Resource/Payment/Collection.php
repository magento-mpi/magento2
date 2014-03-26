<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Resource\Payment;

/**
 * Recurring payment collection
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'recurring_payment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'recurring_payment_collection';

    /**
     * Entity initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\RecurringPayment\Model\Payment', 'Magento\RecurringPayment\Model\Resource\Payment');
    }
}
