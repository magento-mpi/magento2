<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid filter checkbox
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Newsletter_Problem_Grid_Filter_Checkbox extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
     public function getCondition()
    {
        return array();
    }

    public function getHtml()
    {
        return '<input type="checkbox" onclick="problemController.checkCheckboxes(this)"/>';
    }
}// Class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Checkbox END
