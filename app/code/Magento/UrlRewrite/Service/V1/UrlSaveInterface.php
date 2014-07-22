<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

/**
 * Url Save Interface
 */
interface UrlSaveInterface
{
    /**
     * Save url rewrites. Return number of saved urls
     *
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @return void
     */
    public function save(array $urls);
}
