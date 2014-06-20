<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\ConverterFactory;
use Magento\Catalog\Service\V1\Data\Category;
use Magento\Catalog\Service\V1\Data\CategoryBuilder;
use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Category\Info\MetadataBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var CategoryBuilder
     */
    private $builder;

    /**
     * @var CategoryFactory
     */
    private $factory;
    /**
     * @var ConverterFactory
     */
    private $converterFactory;

    /**
     * @param CategoryFactory $factory
     * @param Builder $builder
     */
    public function __construct(CategoryFactory $factory, MetadataBuilder $builder, ConverterFactory $converterFactory)
    {
        $this->builder = $builder;
        $this->factory = $factory;
        $this->converterFactory = $converterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function info($categoryId)
    {
        /** @var CategoryModel $category */
        $category = $this->factory->create();
        $category->load($categoryId);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField(Category::ID, $categoryId);
        }

        $metadata = $this->converterFactory->create(['builder' => $this->builder])
            ->createDataFromModel($category);

        return $metadata;
    }
}
