<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer store attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Source_Store extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('Magento_Core_Model_Resource_Store_Collection');
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => __('Admin')));
            }
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        if(!$value)$value ='0';
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = Mage::getResourceModel('Magento_Core_Model_Resource_Store_Collection');
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = $collection->load()->toOptionArray();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => __('Admin')));
            }
        }

        if ($isMultiple) {
            $values = array();
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        }
        else {
            return $this->_options[$value];
        }
        return false;
    }
}
