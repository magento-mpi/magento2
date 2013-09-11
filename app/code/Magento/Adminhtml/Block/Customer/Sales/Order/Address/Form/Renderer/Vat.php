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
 * VAT ID element renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Sales\Order\Address\Form\Renderer;

class Vat
    extends \Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * Validate button block
     *
     * @var null|\Magento\Adminhtml\Block\Widget\Button
     */
    protected $_validateButton = null;

    protected $_template = 'customer/sales/order/create/address/form/renderer/vat.phtml';

    /**
     * Retrieve validate button block
     *
     * @return \Magento\Adminhtml\Block\Widget\Button
     */
    public function getValidateButton()
    {
        if (is_null($this->_validateButton)) {
            /** @var $form \Magento\Data\Form */
            $form = $this->_element->getForm();

            $vatElementId = $this->_element->getHtmlId();

            $countryElementId = $form->getElement('country_id')->getHtmlId();
            $validateUrl = \Mage::getSingleton('Magento\Backend\Model\Url')
                ->getUrl('*/customer_system_config_validatevat/validateAdvanced');

            $groupSuggestionMessage = __('The customer is currently assigned to Customer Group %s.')
                . ' ' . __('Would you like to change the Customer Group for this order?');

            $vatValidateOptions = \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(array(
                'vatElementId' => $vatElementId,
                'countryElementId' => $countryElementId,
                'groupIdHtmlId' => 'group_id',
                'validateUrl' => $validateUrl,
                'vatValidMessage' => __('The VAT ID is valid. The current Customer Group will be used.'),
                'vatValidAndGroupChangeMessage' => __('Based on the VAT ID, the customer would belong to the Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatInvalidMessage' => __('The VAT ID entered (%s) is not a valid VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatValidationFailedMessage'    => __('There was an error validating the VAT ID. The customer would belong to Customer Group %s.')
                    . "\n" . $groupSuggestionMessage,
                'vatErrorMessage' => __('There was an error validating the VAT ID.')
            ));

            $optionsVarName = $this->getJsVariablePrefix() . 'VatParameters';
            $beforeHtml = '<script type="text/javascript">var ' . $optionsVarName . ' = ' . $vatValidateOptions
                . ';</script>';
            $this->_validateButton = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')->setData(array(
                'label'       => __('Validate VAT Number'),
                'before_html' => $beforeHtml,
                'onclick'     => 'order.validateVat(' . $optionsVarName . ')'
            ));
        }
        return $this->_validateButton;
    }
}
