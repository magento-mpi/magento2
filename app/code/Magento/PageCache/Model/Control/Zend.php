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
 * Zend server page cache control model
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Model\Control;

class Zend implements \Magento\PageCache\Model\Control\ControlInterface
{
    /**
     * Clean zend server page cache
     *
     * @return void
     */
    public function clean()
    {
        if (extension_loaded('Zend Page Cache') && function_exists('page_cache_remove_all_cached_contents')) {
            page_cache_remove_all_cached_contents();
        }
    }
}
