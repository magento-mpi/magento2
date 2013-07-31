<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile info block
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View_Info extends Magento_Adminhtml_Block_Widget
{
    /**
     * Return recurring profile information for view
     *
     * @return array
     */
    public function getRecurringProfileInformation()
    {
        $recurringProfile = Mage::registry('current_recurring_profile');
        $information = array();
        foreach($recurringProfile->getData() as $kay => $value) {
            $information[$recurringProfile->getFieldLabel($kay)] = $value;
        }
        return $information;
    }
}
