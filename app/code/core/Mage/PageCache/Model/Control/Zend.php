<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Zend server page cache control model
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Control_Zend implements Mage_PageCache_Model_Control_Interface
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
