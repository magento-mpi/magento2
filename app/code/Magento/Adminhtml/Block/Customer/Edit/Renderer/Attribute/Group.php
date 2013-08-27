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
 * Renderer for customer group ID
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Renderer_Attribute_Group
    extends Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    protected $_template = 'customer/edit/tab/account/form/renderer/group.phtml';

    /**
     * Customer address
     *
     * @var Magento_Customer_Helper_Address
     */
    protected $_customerAddress = null;

    /**
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerAddress = $customerAddress;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve disable auto group change element HTML ID
     *
     * @return string
     */
    protected function _getDisableAutoGroupChangeElementHtmlId()
    {
        return $this->getDisableAutoGroupChangeAttribute()->getAttributeCode();
    }

    /**
     * Retrieve disable auto group change checkbox label text
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxLabel()
    {
        return __($this->getDisableAutoGroupChangeAttribute()->getFrontend()->getLabel());
    }

    /**
     * Retrieve disable auto group change checkbox state
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxState()
    {
        $customer = Mage::registry('current_customer');
        $checkedByDefault = ($customer && $customer->getId())
            ? false : $this->_customerAddress->getDisableAutoGroupAssignDefaultValue();

        $value = $this->getDisableAutoGroupChangeAttributeValue();
        $state = '';
        if (!empty($value) || $checkedByDefault) {
            $state = 'checked';
        }
        return $state;
    }

    /**
     * Retrieve disable auto group change checkbox element HTML NAME
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxElementName()
    {
        return $this->getElement()->getForm()->getFieldNameSuffix()
            . '[' . $this->_getDisableAutoGroupChangeElementHtmlId() . ']';
    }

    /**
     * Retrieve disable auto group change checkbox element HTML ID
     *
     * @return string
     */
    public function getDisableAutoGroupChangeCheckboxElementId()
    {
        return $this->_getDisableAutoGroupChangeElementHtmlId();
    }
}
