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
 * Renderer for Payflow Link information
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 class Magento_Paypal_Block_Adminhtml_System_Config_Payflowlink_Info
    extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/payflowlink/info.phtml';

    /**
     * Render fieldset html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $columns = ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='$columns'>" . $this->toHtml() . '</td>');
    }
}
