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
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Attribute_Source_Page extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Cms_Model_Resource_Block_Collection')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>__('Please select a static block.')));
        }
        return $this->_options;
    }
}
