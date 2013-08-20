<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog category landing page attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Attribute_Source_Mode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => Mage_Catalog_Model_Category::DM_PRODUCT,
                    'label' => __('Products only'),
                ),
                array(
                    'value' => Mage_Catalog_Model_Category::DM_PAGE,
                    'label' => __('Static block only'),
                ),
                array(
                    'value' => Mage_Catalog_Model_Category::DM_MIXED,
                    'label' => __('Static block and products'),
                )
            );
        }
        return $this->_options;
    }
}
