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
     * @param Link[] $productLinks
     * @return Option
     */
    public function createDataFromModel(OptionModel $option, Product $product, $productLinks = null)
    {
        $this->builder->populateWithArray($option->getData())
            ->setId($option->getId())
            ->setTitle(is_null($option->getTitle()) ? $option->getDefaultTitle() : $option->getTitle())
            ->setSku($product->getSku())
            ->setProductLinks($productLinks);
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
        $optionModel->addData($option->__toArray())
            ->unsetData($optionModel->getIdFieldName())
            ->setParentId($product->getId())
            ->setDefaultTitle($option->getTitle());
        return $optionModel;
    }

    /**
     * @param Option $option
     * @param OptionModel $optionModel
     * @return OptionModel
     */
    public function getModelFromData(Option $option, OptionModel $optionModel)
    {
        $newOptionModel = $this->optionFactory->create();
        $newOptionModel->setData($optionModel->getData())
            ->addData($option->__toArray())
            ->setId($optionModel->getId())
            ->setDefaultTitle(is_null($option->getTitle()) ? $optionModel->getTitle() : $option->getTitle());
        return $newOptionModel;
    }
}
