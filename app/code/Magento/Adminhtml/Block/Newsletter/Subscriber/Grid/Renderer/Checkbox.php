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
 * Adminhtml newsletter subscribers grid checkbox item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Checkbox extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        if($row->getSubscriberStatus()==Magento_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
            return '<input type="checkbox" name="subscriber[]" value="' . $row->getId() . '" class="subscriberCheckbox"/>';
        } else {
            return '';
        }

    }
}// Class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Checkbox END
