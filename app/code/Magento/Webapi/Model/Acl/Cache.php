<?php
/**
 * Webapi ACL cache
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model\Acl;

class Cache extends \Magento\Core\Model\Acl\Cache
{
    /**
     * @param \Magento\Core\Model\Cache\Type\Config $cache
     * @param string $cacheKey
     */
    public function __construct(\Magento\Core\Model\Cache\Type\Config $cache, $cacheKey)
    {
        parent::__construct($cache, $cacheKey);
    }
}
