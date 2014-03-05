<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

/**
 * Cache model for builtin cache
 */
class Cache extends \Magento\App\Cache
{
    /**
     * @var string
     */
    protected $_frontendIdentifier = 'page_cache';
}
