<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog event backend config cache model
 *
 */
namespace Magento\CatalogEvent\Model\Adminhtml\System\Config\Backend;

use Magento\Backend\Block\Menu;
use Magento\Backend\Model\Config\Backend\Cache as BackendCache;

class Cache extends BackendCache implements \Magento\Framework\Object\IdentityInterface
{
    /**
     * Cache tags to clean
     *
     * @var string[]
     */
    protected $_cacheTags = [Menu::CACHE_TAGS];

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Menu::CACHE_TAGS];
    }
}
