<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 */
namespace Magento\PageCache\Helper;

/**
 * Helper for Page Cache module
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Private caching time one year
     */
    const PRIVATE_MAX_AGE_CACHE = 31536000;

    /**
     * Retrieve url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, array $params = array())
    {
        return $this->_getUrl($route, $params);
    }

}
