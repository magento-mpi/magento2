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

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory
     * @param Magento_Core_Model_System_Store $store
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory,
        Magento_Core_Model_System_Store $store
    ) {
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
        $this->_store = $store;
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
