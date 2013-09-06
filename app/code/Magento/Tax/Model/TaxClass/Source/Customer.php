<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Tax_Model_TaxClass_Source_Customer extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Tax_Model_Resource_TaxClass_Collection')
                ->addFieldToFilter('class_type', Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
                ->load()->toOptionArray();
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
