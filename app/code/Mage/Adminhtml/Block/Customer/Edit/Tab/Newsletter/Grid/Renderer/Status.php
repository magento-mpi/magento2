<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                Mage_Newsletter_Model_Queue::STATUS_SENT 	=> Mage::helper('Mage_Customer_Helper_Data')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> Mage::helper('Mage_Customer_Helper_Data')->__('Cancel'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> Mage::helper('Mage_Customer_Helper_Data')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('Mage_Customer_Helper_Data')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> Mage::helper('Mage_Customer_Helper_Data')->__('Paused'),
            );
        parent::_construct();
    }

    public function render(Magento_Object $row)
    {
        return Mage::helper('Mage_Customer_Helper_Data')->__($this->getStatus($row->getQueueStatus()));
    }

    public static function  getStatus($status)
    {
        if(isset(self::$_statuses[$status])) {
            return self::$_statuses[$status];
        }

        return Mage::helper('Mage_Customer_Helper_Data')->__('Unknown');
    }

}
