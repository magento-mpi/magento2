<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation backend config cache model
 *
 */
namespace Magento\Invitation\Model\Adminhtml\System\Config\Backend;

class Cache extends \Magento\Backend\Model\Config\Backend\Cache implements \Magento\Framework\Object\IdentityInterface
{
    /**
     * Cache tags to clean
     *
     * @var string[]
     */
    protected $_cacheTags = [\Magento\Backend\Block\Menu::CACHE_TAGS];

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Backend\Block\Menu::CACHE_TAGS];
    }
}
