<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;

interface DataMapperInterface
{
    /**
     * Map data object
     *
     * @param array $data
     * @return array
     */
    public function map(array $data);
}
