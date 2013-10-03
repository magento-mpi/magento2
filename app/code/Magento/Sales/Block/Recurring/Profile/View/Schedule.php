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
 * Recurring profile view schedule
 */
class Schedule extends \Magento\Sales\Block\Recurring\Profile\View
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
