<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export entity customer model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer_Address
    extends Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract
{
    /**
     * Export process.
     *
     * @return string
     */
    public function export()
    {
        // TODO: Implement export() method.
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        /** @var $collection Mage_Customer_Model_Resource_Address_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Attribute_Collection');
        return $collection->getEntityTypeCode();
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Customer_Model_Resource_Address_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Attribute_Collection');
    }
}
