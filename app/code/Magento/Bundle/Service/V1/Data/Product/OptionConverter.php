<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use Magento\Bundle\Model\Option as OptionModel;
use Magento\Bundle\Model\OptionFactory;
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
     * @var \Magento\Bundle\Model\OptionFactory
     */
    private $optionFactory;

    /**
     * @param OptionBuilder $builder
     * @param OptionFactory $optionFactory
     */
    public function __construct(
        OptionBuilder $builder,
        OptionFactory $optionFactory
    ) {
        $this->builder = $builder;
        $this->optionFactory = $optionFactory;
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
            ->setTitle(is_null($option->getTitle()) ? $option->getDefaultTitle() : $option->getTitle())
            ->setSku($product->getSku());
        return $this->builder->create();
    }

    /**
     * @param Option $option
     * @param Product $product
     * @return OptionModel
     */
    public function createModelFromData(Option $option, Product $product)
    {
        $optionModel = $this->optionFactory->create();
        $optionModel->setParentId($product->getId())
            ->setType($option->getType())
            ->setPosition($option->getPosition())
            ->setRequired($option->isRequired())
            ->setDefaultTitle($option->getTitle())
            ->setTitle($option->getTitle());
        return $optionModel;
    }
}
