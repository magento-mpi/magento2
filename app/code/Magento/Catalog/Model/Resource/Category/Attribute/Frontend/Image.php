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
 * Category image attribute frontend
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Frontend_Image
    extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    const IMAGE_PATH_SEGMENT = 'catalog/category/';

    /**
     * Return image url
     *
     * @param Magento_Object $object
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
