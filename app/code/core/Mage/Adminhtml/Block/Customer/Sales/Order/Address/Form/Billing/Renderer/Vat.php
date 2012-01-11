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
 * VAT ID element renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Sales_Order_Address_Form_Billing_Renderer_Vat
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    /**
     * Validate button block
     *
     * @var null|Mage_Adminhtml_Block_Widget_Button
     */
    protected $_validateButton = null;

    /**
     * Set custom template for 'VAT number'
     */
    protected function _construct()
    {
        $this->setTemplate('customer/sales/order/create/billing/form/renderer/vat.phtml');
    }

    /**
     * Retrieve validate button block
     *
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    public function getValidateButton()
    {
        if (is_null($this->_validateButton)) {
            /** @var $form Varien_Data_Form */
            $form = $this->_element->getForm();

            $vatElementId = $this->_element->getHtmlId();

            /** @var $formAccountBlock Mage_Adminhtml_Block_Sales_Order_Create_Form_Account */
            $formAccountBlock = $this->getLayout()->getBlock('form_account');
            $groupIdHtmlId = $formAccountBlock->getForm()->getElement('group_id')->getHtmlId();

            $countryElementId = $form->getElement('country_id')->getHtmlId();
            $validateUrl = Mage::getSingleton('Mage_Adminhtml_Model_Url')
                ->getUrl('*/customer_system_config_validatevat/validateAdvanced');

            $vatValidateOptions = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'vatElementId'                  => $vatElementId,
                'countryElementId'              => $countryElementId,
                'groupIdHtmlId'                 => $groupIdHtmlId,
                'validateUrl'                   => $validateUrl,
                'vatValidMessage'               => Mage::helper('Mage_Customer_Helper_Data')->__('The VAT ID is valid. The current Customer Group will be used.'),
                'vatValidAndGroupChangeMessage' =>
                    Mage::helper('Mage_Customer_Helper_Data')->__('Based on the VAT ID, the customer would belong to Customer Group %s.') . "\n"
                    . Mage::helper('Mage_Customer_Helper_Data')->__('The customer is currently assigned to Customer Group %s.') . ' '
                    . Mage::helper('Mage_Customer_Helper_Data')->__('Would you like to change the Customer Group for this order?'),
                'vatInvalidMessage' => Mage::helper('Mage_Customer_Helper_Data')->__('The VAT ID entered (%s) is not valid VAT ID.'),
                'vatValidationFailedMessage'    => Mage::helper('Mage_Customer_Helper_Data')->__('There was an error validating the VAT ID. Please try again later.'),
            ));

            $beforeHtml = '<script type="text/javascript">var vatValidateOptions = '
                . $vatValidateOptions . ';</script>';
            $this->_validateButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label'       => Mage::helper('Mage_Customer_Helper_Data')->__('Validate VAT Number'),
                'before_html' => $beforeHtml,
                'onclick'     => "order.validateVat(vatValidateOptions)"
            ));
        }
        return $this->_validateButton;
    }
}
