<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\View;

/**
 * Recurring payment view reference
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Reference extends \Magento\RecurringPayment\Block\Payment\View
{
    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\RecurringPayment\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\RecurringPayment\Block\Fields $fields,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->_fields = $fields;
    }

    /**
     * Prepare reference info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        foreach (['method_code', 'reference_id', 'schedule_description', 'state'] as $key) {
            $this->_addInfo(
                [
                    'label' => $this->_fields->getFieldLabel($key),
                    'value' => $this->_recurringPayment->renderData($key),
                ]
            );
        }
    }
}
