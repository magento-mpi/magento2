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
 * Customer website attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Source_Website extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_store;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_System_Store $store
    ) {
        $this->_store = $store;
        parent::__construct($coreData);
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_store->getWebsiteValuesForForm(true, true);
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
