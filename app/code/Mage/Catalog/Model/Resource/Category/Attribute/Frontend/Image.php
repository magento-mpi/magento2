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
 * Category image attribute frontend
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Frontend_Image
    extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    const IMAGE_PATH_SEGMENT = 'catalog/category/';

    /**
     * Return image url
     *
     * @param Varien_Object $object
     * @return string|null
     */
    public function getUrl($object)
    {
        $url = false;
        if ($image = $object->getData($this->getAttribute()->getAttributeCode())) {
            $url = Mage::getBaseUrl('media') . self::IMAGE_PATH_SEGMENT . $image;
        }
        return $url;
    }
}