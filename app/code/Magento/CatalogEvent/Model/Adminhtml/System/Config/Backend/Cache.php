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

use Magento\Backend\Block\Menu;
use Magento\Backend\Model\Config\Backend\Cache as BackendCache;

class Cache extends BackendCache
{
    /**
     * Cache tags to clean
     *
     * @var string[]
     */
    protected $_cacheTags = array(
        Menu::CACHE_TAGS
    );
}
