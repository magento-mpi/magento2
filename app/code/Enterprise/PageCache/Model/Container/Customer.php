<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Container for cache lifetime equated with session lifetime
 */
class Enterprise_PageCache_Model_Container_Customer extends Enterprise_PageCache_Model_Container_Abstract
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
        $lifetime = Mage::getConfig()->getNode(Magento_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME);
        return parent::_saveCache($data, $id, $tags, $lifetime);
    }
}
