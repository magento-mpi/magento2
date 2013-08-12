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
 * Customer region attribute source
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Address_Attribute_Source_Region extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retreive all region options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Directory_Model_Resource_Region_Collection')
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
