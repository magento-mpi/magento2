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
     * @param string $productSku
     * @return \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLink[]
     */
    public function getList($productSku);
}