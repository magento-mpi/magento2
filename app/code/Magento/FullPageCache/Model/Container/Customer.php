<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Container for cache lifetime equated with session lifetime
 */
namespace Magento\FullPageCache\Model\Container;

class Customer extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * Save data to cache storage and set cache lifetime equal with default cookie lifetime
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $lifetime = \Mage::getConfig()->getValue(\Magento\Core\Model\Cookie::XML_PATH_COOKIE_LIFETIME, 'default');
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
