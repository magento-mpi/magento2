<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

/**
 * Url Persist Interface
 */
interface UrlPersistInterface
{
    /**
     * Save new url rewrites and remove old if exist.
     *
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function replace(array $urls);

    /**
     * Remove rewrites that contains some rewrites data
     *
     * @param array $data
     * @return void
     */
    public function deleteByData(array $data);
}
