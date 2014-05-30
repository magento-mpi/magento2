<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLink\DataMapper;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapperInterface;

class GroupedProduct implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function map(array $data)
    {
        foreach ($data as &$item) {
            if (isset($item['custom_attributes']['qty']['value'])) {
                $item['qty'] = $item['custom_attributes']['qty']['value'];
            }
        }
        return $data;
    }
}
