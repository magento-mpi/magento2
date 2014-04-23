<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Url;

interface ScopeInterface extends \Magento\Framework\App\ScopeInterface
{
    /**
     * Retrieve base URL
     *
     * @param string $type
     * @param boolean|null $secure
     * @return string
     */
    public function getBaseUrl($type = \Magento\UrlInterface::URL_TYPE_LINK, $secure = null);

    /**
     * Check is URL should be secure
     *
     * @return boolean
     */
    public function isUrlSecure();
}
