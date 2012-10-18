<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Clone model for media images related config fields
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Clone_Media_Image extends Mage_Core_Model_Config_Data
{

    /**
     * Get fields prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
        //$entityType = Mage::getModel('Mage_Eav_Model_Entity_Type');
        /* @var $entityType Mage_Eav_Model_Entity_Type */
        //$entityTypeId = $entityType->loadByCode('catalog_product')->getEntityTypeId();

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
