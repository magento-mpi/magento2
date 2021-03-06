<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
