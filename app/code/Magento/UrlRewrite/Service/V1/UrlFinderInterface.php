<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1;

/**
 * Url Matcher Interface
 */
interface UrlFinderInterface
{
    /**
     * Find row by specific data
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    public function findOneByData(array $data);

    /**
     * Find rows by specific filter
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByData(array $data);
}
