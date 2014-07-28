<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\Data\Filter;

/**
 * Url Persist Interface
 */
interface UrlPersistInterface
{
    /**
     * Save url rewrites. Return number of saved urls
     *
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @throws \InvalidArgumentException
     * @return void
     */
    public function save(array $urls);

    /**
     * Remove rewrites by filter
     *
     * @param Filter $filter
     * @return void
     */
    public function deleteByFilter(Filter $filter);
}
