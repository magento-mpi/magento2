<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category\Info;

use Magento\Catalog\Model\Category;

/**
 * Class Converter
 * @codeCoverageIgnore
 */
class Converter
{
    /**
     * @var MetadataBuilder
     */
    private $builder;

    /**
     * @param MetadataBuilder $builder
     */
    public function __construct(MetadataBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Category $category
     * @return Metadata
     */
    public function createDataFromModel(Category $category)
    {
        $builder = $this->builder->populateWithArray($category->getData())
            ->setCategoryId($category->getId())
            ->setActive($category->getIsActive())
            ->setChildren($category->getAllChildren(true));

        return $builder->create();
    }
}
