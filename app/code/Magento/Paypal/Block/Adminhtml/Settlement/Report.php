<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Adminhtml_Settlement_Report extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Prepare grid container, add additional buttons
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Paypal';
        $this->_controller = 'adminhtml_settlement_report';
        $this->_headerText = Mage::helper('Magento_Paypal_Helper_Data')->__('PayPal Settlement Reports');
        parent::_construct();
        $this->_removeButton('add');
        $message = Mage::helper('Magento_Paypal_Helper_Data')->__('We are connecting to the PayPal SFTP server to retrieve new reports. Are you sure you want to continue?');
        if (true == $this->_authorization->isAllowed('Magento_Paypal::fetch')) {
            $this->_addButton('fetch', array(
                'label'   => Mage::helper('Magento_Paypal_Helper_Data')->__('Fetch Updates'),
                'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('*/*/fetch')}')",
                'class'   => 'task'
            ));
        }
    }
}
