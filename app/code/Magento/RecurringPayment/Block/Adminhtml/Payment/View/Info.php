<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payment info block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Payment\View;

class Info extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\RecurringPayment\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\RecurringPayment\Block\Fields $fields,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
    }

    /**
     * Return recurring payment information for view
     *
     * @return array
     */
    public function getRecurringPaymentInformation()
    {
        $recurringPayment = $this->_coreRegistry->registry('current_recurring_payment');
        $information = [];
        foreach ($recurringPayment->getData() as $key => $value) {
            $information[$this->_fields->getFieldLabel($key)] = $value;
        }
        return $information;
    }
}
