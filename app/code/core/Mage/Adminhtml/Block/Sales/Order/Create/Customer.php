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
 * Adminhtml sales order create block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Customer extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Buttons to print in own template if any
     * @var array
     */
    protected $_buttonData;


    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_customer');
        $buttonData = array(
            'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Create New Customer'),
            'onclick'   => 'order.setCustomerId(false)',
            'class'     => 'action-add',
        );
        $contentBlock = $this->getLayout()->getBlock('content');
        if ($contentBlock) {
            $id = $this->helper('Mage_Core_Helper_Data')->uniqHash('id_');
            $contentBlock->addButton($id, $buttonData);
        } else {
            $this->_buttonData = $buttonData;
        }
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Please Select a Customer');
    }

    public function getButtonsHtml()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($this->_buttonData)->toHtml();
    }

}
