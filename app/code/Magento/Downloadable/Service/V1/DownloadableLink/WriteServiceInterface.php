<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink;

use \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLink;

interface WriteServiceInterface
{
    /**
     * Add link(or sample) for specified product
     *
     * @param string $productSku
     * @param \Magento\Downloadable\Service\V1\DownloadableLink\Data\DownloadableLink $linkData
     * @return int link ID
     */
    public function add($productSku, DownloadableLink $linkData);

    /**
     * Remove link by id
     *
     * @param int $linkId
     * @return bool
     */
    public function remove($linkId);
}