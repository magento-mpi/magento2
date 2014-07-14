<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1;

/**
 * Url Persister Interface
 */
interface UrlPersisterInterface
{
    /**
     * Save url rewrites. Return number of saved urls
     *
     * @param \Magento\UrlRedirect\Model\Data\UrlRewrite[] $urls
     */
    public function save(array $urls);
}
