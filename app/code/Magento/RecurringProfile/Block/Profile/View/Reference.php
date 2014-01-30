<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Block\Profile\View;

/**
 * Recurring profile view reference
 */
class Reference extends \Magento\RecurringProfile\Block\Profile\View
{
    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
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
        foreach (array('method_code', 'reference_id', 'schedule_description', 'state') as $key) {
            $this->_addInfo(array(
                'label' => $this->_fields->getFieldLabel($key),
                'value' => $this->_recurringProfile->renderData($key),
            ));
        }
    }
}
