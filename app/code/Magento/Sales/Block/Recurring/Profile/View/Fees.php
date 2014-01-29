<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Recurring profile view fees
 */
class Fees extends \Magento\Sales\Block\Recurring\Profile\View
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
    ) {
        $this->_coreHelper = $coreHelper;
        parent::__construct($context, $registry, $data);
        $this->_fields = $fields;
    }

    /**
     * Prepare fees info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        $this->_addInfo(array(
            'label' => $this->_fields->getFieldLabel('currency_code'),
            'value' => $this->_recurringProfile->getCurrencyCode()
        ));
        $params = array('init_amount', 'trial_billing_amount', 'billing_amount', 'tax_amount', 'shipping_amount');
        foreach ($params as $key) {
            $value = $this->_recurringProfile->getData($key);
            if ($value) {
                $this->_addInfo(array(
                    'label' => $this->_fields->getFieldLabel($key),
                    'value' => $this->_coreHelper->formatCurrency($value, false),
                    'is_amount' => true,
                ));
            }
        }
    }
}
