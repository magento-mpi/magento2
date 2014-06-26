<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\MetadataBuilder;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface;

class Select implements ReaderInterface
{
    /**
     * @var MetadataBuilder
     */
    protected $valueBuilder;

    /**
     * @param MetadataBuilder $valueBuilder
     */
    public function __construct(MetadataBuilder $valueBuilder)
    {
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function read(\Magento\Catalog\Model\Product\Option $option)
    {
        $output = [];
        foreach ($option->getValues() as $value) {
            $output[] = $this->valueBuilder->populateWithArray(
                [
                    Metadata::PRICE => $value->getPrice(),
                    Metadata::PRICE_TYPE => $value->getPriceType(),
                    Metadata::SKU => $value->getSku(),
                    Metadata::TITLE => $value->getTitle(),
                    Metadata::SORT_ORDER => $value->getSortOrder(),
                    Metadata::ID => $value->getId()
                ]
            )->create();
        }
        return $output;
    }
}
