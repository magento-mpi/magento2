<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Used in creating options for Caching Application config value selection
 */
namespace Magento\PageCache\Model\System\Config\Source;

/**
 * Class Application
 *
 * @package Magento\PageCache\Model\System\Config\Source
 */
class Application implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>__('Built-in Application')),
            array('value' => 1, 'label'=>__('Varnish Caching')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => __('Built-in Application'),
            1 => __('Varnish Caching'),
        );
    }
} 
