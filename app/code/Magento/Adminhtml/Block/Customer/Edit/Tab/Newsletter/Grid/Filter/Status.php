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
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Filter_Status extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                null                                        => null,
                Magento_Newsletter_Model_Queue::STATUS_SENT    => Mage::helper('Magento_Customer_Helper_Data')->__('Sent'),
                Magento_Newsletter_Model_Queue::STATUS_CANCEL  => Mage::helper('Magento_Customer_Helper_Data')->__('Cancel'),
                Magento_Newsletter_Model_Queue::STATUS_NEVER   => Mage::helper('Magento_Customer_Helper_Data')->__('Not Sent'),
                Magento_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('Magento_Customer_Helper_Data')->__('Sending'),
                Magento_Newsletter_Model_Queue::STATUS_PAUSE   => Mage::helper('Magento_Customer_Helper_Data')->__('Paused'),
            );
        parent::_construct();
    }

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>Mage::helper('Magento_Customer_Helper_Data')->__($label));
        }

        return $result;
    }

    public function getCondition()
    {
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}
