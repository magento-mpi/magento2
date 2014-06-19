<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValueBuilder;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface;

class Select implements ReaderInterface
{
    /**
     * @var OptionValueBuilder
     */
    protected $valueBuilder;

    /**
     * @param OptionValueBuilder $valueBuilder
     */
    public function __construct(OptionValueBuilder $valueBuilder)
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
                    OptionValue::PRICE => $value->getPrice(),
                    OptionValue::PRICE_TYPE => $value->getPriceType(),
                    OptionValue::SKU => $value->getSku(),
                    OptionValue::TITLE => $value->getTitle(),
                    OptionValue::SORT_ORDER => $value->getSortOrder()
                ]
            )->create();
        }
        return $output;
    }
}
