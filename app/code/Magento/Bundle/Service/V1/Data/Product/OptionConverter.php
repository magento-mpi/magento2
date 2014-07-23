<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use Magento\Bundle\Model\Option as OptionModel;
use Magento\Catalog\Model\Product;

/**
 * @codeCoverageIgnore
 */
class OptionConverter
{
    /**
     * @var OptionBuilder
     */
    private $builder;

    /**
     * @param OptionBuilder $builder
     */
    public function __construct(OptionBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param OptionModel $option
     * @param Product $product
     * @return Option
     */
    public function createDataFromModel(OptionModel $option, Product $product)
    {
        $this->builder->populateWithArray($option->getData())
            ->setId($option->getId())
            ->setTitle($option->getDefaultTitle())
            ->setSku($product->getSku());
        return $this->builder->create();
    }
}
