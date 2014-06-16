<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1;

interface DownloadableLinkReadServiceInterface
{
    /**
     * @param string $productSku
     * @return \Magento\Downloadable\Service\V1\Data\Link[]
     */
    public function getList($productSku);
}