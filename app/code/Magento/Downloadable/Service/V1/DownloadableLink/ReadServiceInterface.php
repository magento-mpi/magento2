<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink;

interface ReadServiceInterface
{
    /**
     * List of samples for downloadable product
     *
     * @param string $productSku
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableSampleInfo[]
     */
    public function samples($productSku);

    /**
     * List of links with associated samples
     *
     * @param string $productSku
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLinkInfo[]
     */
    public function getList($productSku);
}
