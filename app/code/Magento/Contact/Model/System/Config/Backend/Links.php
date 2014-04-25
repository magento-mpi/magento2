<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Contact\Model\System\Config\Backend;

/**
 * Cache cleaner backend model
 */
class Links extends \Magento\Backend\Model\Config\Backend\Cache implements \Magento\Framework\Object\IdentityInterface
{
    /**
     * Cache tags to clean
     *
     * @var string[]
     */
    protected $_cacheTags = array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG);

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG);
    }
}
