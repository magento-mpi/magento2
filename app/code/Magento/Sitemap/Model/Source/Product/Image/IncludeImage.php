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
namespace Magento\Sitemap\Model\Source\Product\Image;

class IncludeImage
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
