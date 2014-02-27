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
class Application implements \Magento\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\PageCache\Model\Config::BUILT_IN, 'label'=>__('Built-in Application')),
            array('value' => \Magento\PageCache\Model\Config::VARNISH, 'label'=>__('Varnish Caching')),
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
            \Magento\PageCache\Model\Config::BUILT_IN => __('Built-in Application'),
            \Magento\PageCache\Model\Config::VARNISH => __('Varnish Caching'),
        );
    }
}
