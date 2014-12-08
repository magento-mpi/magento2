<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\View;

/**
 * Recurring payment view fees
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Fees extends \Magento\RecurringPayment\Block\Payment\View
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\RecurringPayment\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\RecurringPayment\Block\Fields $fields,
        array $data = []
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
        $this->_addInfo(
            [
                'label' => $this->_fields->getFieldLabel('currency_code'),
                'value' => $this->_recurringPayment->getCurrencyCode(),
            ]
        );
        $params = ['init_amount', 'trial_billing_amount', 'billing_amount', 'tax_amount', 'shipping_amount'];
        foreach ($params as $key) {
            $value = $this->_recurringPayment->getData($key);
            if ($value) {
                $this->_addInfo(
                    [
                        'label' => $this->_fields->getFieldLabel($key),
                        'value' => $this->_coreHelper->formatCurrency($value, false),
                        'is_amount' => true,
                    ]
                );
            }
        }
    }
}
