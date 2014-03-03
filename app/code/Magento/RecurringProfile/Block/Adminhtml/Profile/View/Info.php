<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile info block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringProfile\Block\Adminhtml\Profile\View;

class Info extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
    }

    /**
     * Return recurring profile information for view
     *
     * @return array
     */
    public function getRecurringProfileInformation()
    {
        $recurringProfile = $this->_coreRegistry->registry('current_recurring_profile');
        $information = array();
        foreach ($recurringProfile->getData() as $key => $value) {
            $information[$this->_fields->getFieldLabel($key)] = $value;
        }
        return $information;
    }
}
