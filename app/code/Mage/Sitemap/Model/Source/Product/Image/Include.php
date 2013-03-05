<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image include policy into sitemap file
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Source_Product_Image_Include
{
    /**#@+
     * Add Images into Sitemap possible values
     */
    const INCLUDE_NONE = 'none';
    const INCLUDE_BASE = 'base';
    const INCLUDE_ALL  = 'all';
    /**#@-*/

    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::INCLUDE_NONE => Mage::helper('Mage_Sitemap_Helper_Data')->__('None'),
            self::INCLUDE_BASE => Mage::helper('Mage_Sitemap_Helper_Data')->__('Base Only'),
            self::INCLUDE_ALL  => Mage::helper('Mage_Sitemap_Helper_Data')->__('All'),
        );
    }
}
