<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer website attribute source
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Source_Website extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(true, true);
        }

        return $this->_options;
    }

    public function getOptionText($value)
    {
        if (!$this->_options) {
          $this->_options = $this->getAllOptions();
        }
        foreach ($this->_options as $option) {
          if ($option['value'] == $value) {
            return $option['label'];
          }
        }
        return false;
    }
}
