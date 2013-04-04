<?php
/**
 * {license_notice}
 *
 * @category    
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TheFind feed helper
 *
 * @category   Find
 * @package    Find_Feed
 */
class Find_Feed_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Checking if some required attributes missed
     *
     * @param array $attributes
     * @return bool
     */
    public function checkRequired($attributes) 
    {
        $attributeConfig = Mage::getConfig()->getNode(Find_Feed_Model_Import::XML_NODE_FIND_FEED_ATTRIBUTES);
        $attributeRequired = array();
        foreach ($attributeConfig->children() as $ac) {
            if ((int)$ac->required) {
                $attributeRequired[] = (string)$ac->label;
            }
        }

        foreach ($attributeRequired as $value) {
            if (!isset($attributes[$value])) { 
                return false;
            }
        }
        return true;
    }

    /**
     * Product entity type
     *
     * @return int
     */
    public function getProductEntityType()
    {
        return Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('catalog_product')->getId();
    }
}
