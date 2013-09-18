<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity/Attribute/Model - select product design options container from config
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Entity_Product_Attribute_Design_Options_Container extends Magento_Eav_Model_Entity_Attribute_Source_Config
{
    protected $_configNodePath;

    public function __construct(Magento_Core_Model_Config $coreConfig)
    {
        parent::__construct($coreConfig);
        $this->_configNodePath = 'global/catalog/product/design/options_container';
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        if (sizeof($options) > 0) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    return $option['label'];
                }
            }
        }
        if (isset($options[$value])) {
            return $option[$value];
        }
        return false;
    }

}
