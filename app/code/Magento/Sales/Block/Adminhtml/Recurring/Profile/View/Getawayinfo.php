<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile getaway info block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View;

class Getawayinfo extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\RecurringProfile\Block\Fields $fields,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
    }

    /**
     * Return recurring profile getaway information for view
     *
     * @return array
     */
    public function getRecurringProfileGetawayInformation()
    {
        $recurringProfile = $this->_coreRegistry->registry('current_recurring_profile');
        $information = array();
        foreach ($recurringProfile->getData() as $key => $value) {
            $information[$this->_fields->getFieldLabel($key)] = $value;
        }
        return $information;
    }
}
