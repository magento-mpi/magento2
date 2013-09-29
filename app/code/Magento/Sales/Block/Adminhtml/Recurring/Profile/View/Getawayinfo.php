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
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View;

class Getawayinfo extends \Magento\Adminhtml\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
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
        foreach ($recurringProfile->getData() as $kay => $value) {
            $information[$recurringProfile->getFieldLabel($kay)] = $value;
        }
        return $information;
    }
}
