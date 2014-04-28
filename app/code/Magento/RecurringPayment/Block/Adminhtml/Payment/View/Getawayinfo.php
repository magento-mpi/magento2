<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payment getaway info block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Payment\View;

class Getawayinfo extends \Magento\Backend\Block\Widget
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
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
    }

    /**
     * Return recurring payment getaway information for view
     *
     * @return array
     */
    public function getRecurringPaymentGetawayInformation()
    {
        $recurringPayment = $this->_coreRegistry->registry('current_recurring_payment');
        $information = array();
        foreach ($recurringPayment->getData() as $key => $value) {
            $information[$this->_fields->getFieldLabel($key)] = $value;
        }
        return $information;
    }
}
