<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image include policy into sitemap file
 *
 * @category   Magento
 * @package    Magento_Sitemap
 */
class Magento_Sitemap_Model_Source_Product_Image_Include implements Magento_Core_Model_Option_ArrayInterface
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
            self::INCLUDE_NONE => __('None'),
            self::INCLUDE_BASE => __('Base Only'),
            self::INCLUDE_ALL  => __('All'),
        );
    }
}
