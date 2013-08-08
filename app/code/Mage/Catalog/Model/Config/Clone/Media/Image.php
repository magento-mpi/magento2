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
 * Clone model for media images related config fields
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Config_Clone_Media_Image extends Magento_Core_Model_Config_Data
{

    /**
     * Get fields prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
        // use cached eav config
        $entityTypeId = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getId();

        /* @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Attribute_Collection');
        $collection->setEntityTypeFilter($entityTypeId);
        $collection->setFrontendInputTypeFilter('media_image');

        $prefixes = array();

        foreach ($collection as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $prefixes[] = array(
                'field' => $attribute->getAttributeCode() . '_',
                'label' => $attribute->getFrontend()->getLabel(),
            );
        }

        return $prefixes;
    }

}
