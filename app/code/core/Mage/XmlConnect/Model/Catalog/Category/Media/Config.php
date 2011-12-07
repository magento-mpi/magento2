<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog category media config
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Catalog_Category_Media_Config extends Mage_Catalog_Model_Product_Media_Config
{
    /**
     * Getter, return Catalog baseMediaPath
     *
     * @return string
     */
    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category';
    }

    /**
     * Getter, return catalog baseMediaUrl
     *
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'catalog/category';
    }

    /**
     * Getter, return  catalog baseMedia temporary dir path
     *
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'tmp' . DS . 'catalog' . DS . 'category';
    }

    /**
     * Getter, return  catalog baseMedia temporary dir URL
     *
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'tmp/catalog/category';
    }
}
