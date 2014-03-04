<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation backend config cache model
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Model\Adminhtml\System\Config\Backend;

class Cache
    extends \Magento\Backend\Model\Config\Backend\Cache
    implements \Magento\Object\IdentityInterface
{
    /**
     * Cache tags to clean
     *
     * @var string[]
     */
    protected $_cacheTags = array(
        \Magento\Backend\Block\Menu::CACHE_TAGS
    );

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(\Magento\Backend\Block\Menu::CACHE_TAGS);
    }
}
