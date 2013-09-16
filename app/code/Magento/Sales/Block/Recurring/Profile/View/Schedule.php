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
 * Recurring profile view schedule
 */
class Magento_Sales_Block_Recurring_Profile_View_Schedule extends Magento_Sales_Block_Recurring_Profile_View
{
    /**
     * Prepare schedule info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        foreach (array('start_datetime', 'suspension_threshold') as $key) {
            $this->_addInfo(array(
                'label' => $this->_profile->getFieldLabel($key),
                'value' => $this->_profile->renderData($key),
            ));
        }

        foreach ($this->_profile->exportScheduleInfo() as $info) {
            $this->_addInfo(array(
                'label' => $info->getTitle(),
                'value' => $info->getSchedule(),
            ));
        }
    }
}
