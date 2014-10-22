<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;

class CategoryRepository implements \Magento\Catalog\Api\CategoryRepositoryInterface
{
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Resource\Category
     */
    protected $categoryResource;

    /**
     * List of fields that can used config values in case when value does not defined directly
     *
     * @var array
     */
    protected $useConfigFields = ['available_sort_by', 'default_sort_by', 'filter_price_range'];

    /**
     * @param CategoryFactory $categoryFactory
     * @param Resource\Category $categoryResource
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Category $categoryResource,
        \Magento\Framework\StoreManagerInterface $storeManager

    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        try {
            $parentId = $category->getParentId() ?: $this->storeManager->getStore()->getRootCategoryId();
            $parentCategory = $this->get($parentId);
            /** @var  $category Category */
            $category->setPath($parentCategory->getPath());

            $this->validateCategory($category);
            $this->categoryResource->save($category);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save category: %message', ['message' => $e->getMessage()], $e);
        }
        return $category->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function get($categoryId)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $categoryId);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField('id', $categoryId);
        }
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        try {
            $this->categoryResource->delete($category);
        } catch (\Exception $e) {
            throw new StateException('Cannot delete category with id %category_id',
                ['category_id' => $category->getId()], $e);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIdentifier($categoryId)
    {
        $category = $this->get($categoryId);
        return  $this->delete($category);
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
                    $attribute = $this->categoryResource->getAttribute($code)->getFrontend()->getLabel();
                    throw new \Magento\Framework\Model\Exception(__('Attribute "%1" is required.', $attribute));
                } else {
                    throw new \Magento\Framework\Model\Exception($error);
                }
            }
        }
        $category->unsetData('use_post_data_config');
    }
}
