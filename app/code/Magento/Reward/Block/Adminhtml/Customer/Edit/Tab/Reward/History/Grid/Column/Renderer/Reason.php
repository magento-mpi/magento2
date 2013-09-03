<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for messages in reward history grid
 *
 */
class Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid_Column_Renderer_Reason
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render "Expired / not expired" reward "Reason" field
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $expired = '';
        if ($row->getData('is_duplicate_of') !== null) {
             $expired = '<em>' . __('Expired reward') . '</em> ';
        }
        return $expired . (parent::_getValue($row));
    }
}
