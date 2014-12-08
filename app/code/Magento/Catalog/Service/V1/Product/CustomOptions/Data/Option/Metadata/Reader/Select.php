<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\MetadataBuilder;

class Select implements ReaderInterface
{
    /**
     * @var MetadataBuilder
     */
    protected $metadataBuilder;

    /**
     * @param MetadataBuilder $metadataBuilder
     */
    public function __construct(MetadataBuilder $metadataBuilder)
    {
        $this->metadataBuilder = $metadataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function read(\Magento\Catalog\Model\Product\Option $option)
    {
        $output = [];
        foreach ($option->getValues() as $value) {
            $output[] = $this->metadataBuilder->populateWithArray(
                [
                    Metadata::PRICE => $value->getPrice(),
                    Metadata::PRICE_TYPE => $value->getPriceType(),
                    Metadata::SKU => $value->getSku(),
                    Metadata::TITLE => $value->getTitle(),
                    Metadata::SORT_ORDER => $value->getSortOrder(),
                    Metadata::OPTION_TYPE_ID => $value->getId(),
                ]
            )->create();
        }
        return $output;
    }
}
