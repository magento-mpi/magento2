<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Service\V1\Data\Category as CategoryDataObject;
use Magento\Catalog\Service\V1\Data\CategoryMapper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Data\CategoryMapper
     */
    private $categoryMapper;

    /**
     * List of fields that can used config values in case when value does not defined directly
     *
     * @var array
     */
    private $useConfigFields = ['available_sort_by', 'default_sort_by', 'filter_price_range'];

    /**
     * @param CategoryFactory $categoryFactory
     * @param CategoryMapper $categoryMapper
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        CategoryMapper $categoryMapper
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryMapper = $categoryMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function create(CategoryDataObject $category)
    {
        try {
            $categoryModel = $this->categoryMapper->toModel($category);
            $this->validateCategory($categoryModel);
            $categoryModel->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save category', [], $e);
        }
        return $categoryModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($categoryId)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $category->load($categoryId);

        if (!$category || !$category->getId()) {
            throw NoSuchEntityException::singleField(CategoryDataObject::ID, $categoryId);
        }

        try {
            $category->delete();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Cannot delete category with id %1', [$categoryId], $e);
        }

        return true;
    }

    /**
     * Validate category process
     *
     * @param  Category $category
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function validateCategory(Category $category)
    {
        $useConfigFields = [];
        foreach ($this->useConfigFields as $field) {
            if (!$category->getData($field)) {
                $useConfigFields[] = $field;
            }
        }
        $category->setData('use_post_data_config', $useConfigFields);
        $validate = $category->validate();
        if ($validate !== true) {
            foreach ($validate as $code => $error) {
                if ($error === true) {
                    $attribute = $category->getResource()->getAttribute($code)->getFrontend()->getLabel();
                    throw new \Magento\Framework\Model\Exception(__('Attribute "%1" is required.', $attribute));
                } else {
                    throw new \Magento\Framework\Model\Exception($error);
                }
            }
        }
        $category->unsetData('use_post_data_config');
    }
}
