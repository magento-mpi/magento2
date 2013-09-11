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
 * Recurring profile info block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile\View;

class Info extends \Magento\Adminhtml\Block\Widget
{
    /**
     * Return recurring profile information for view
     *
     * @return array
     */
    public function getRecurringProfileInformation()
    {
        $recurringProfile = \Mage::registry('current_recurring_profile');
        $information = array();
        foreach($recurringProfile->getData() as $kay => $value) {
            $information[$recurringProfile->getFieldLabel($kay)] = $value;
        }
        return $information;
    }
}
