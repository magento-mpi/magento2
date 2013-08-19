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
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                Magento_Newsletter_Model_Queue::STATUS_SENT 	=> __('Sent'),
                Magento_Newsletter_Model_Queue::STATUS_CANCEL	=> __('Cancel'),
                Magento_Newsletter_Model_Queue::STATUS_NEVER 	=> __('Not Sent'),
                Magento_Newsletter_Model_Queue::STATUS_SENDING => __('Sending'),
                Magento_Newsletter_Model_Queue::STATUS_PAUSE 	=> __('Paused'),
            );
        parent::_construct();
    }

    public function render(Magento_Object $row)
    {
        return __($this->getStatus($row->getQueueStatus()));
    }

    public static function  getStatus($status)
    {
        if(isset(self::$_statuses[$status])) {
            return self::$_statuses[$status];
        }

        return __('Unknown');
    }

}
