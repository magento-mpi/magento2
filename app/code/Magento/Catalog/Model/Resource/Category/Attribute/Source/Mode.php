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
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Source_Mode extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Returns all mode options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => Magento_Catalog_Model_Category::DM_PRODUCT,
                    'label' => __('Products only'),
                ),
                array(
                    'value' => Magento_Catalog_Model_Category::DM_PAGE,
                    'label' => __('Static block only'),
                ),
                array(
                    'value' => Magento_Catalog_Model_Category::DM_MIXED,
                    'label' => __('Static block and products'),
                )
            );
        }
        return $this->_options;
    }
}
