<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog event backend config cache model
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
namespace Magento\CatalogEvent\Model\Adminhtml\System\Config\Backend;

class Cache
    extends \Magento\Backend\Model\Config\Backend\Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        \Magento\Backend\Block\Menu::CACHE_TAGS
    );
}
