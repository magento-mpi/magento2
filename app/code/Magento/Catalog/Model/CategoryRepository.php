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
     * @var \Magento\Catalog\Api\Data\CategoryInterfaceDataBuilder
     */
    protected $categoryBuilder;

    /**
     * @param CategoryFactory $categoryFactory
     * @param Resource\Category $categoryResource
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Category $categoryResource,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\Data\CategoryInterfaceDataBuilder $dataBuilder

    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->storeManager = $storeManager;
        $this->categoryBuilder = $dataBuilder;
    }

    /**
     * Create category service
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        if ($category->getId()) {
            $existingCategory = $this->get($category->getId());
            $existingData = $category->getData();
            if (isset($existingData['image']) && is_array($existingData['image'])) {
                $existingData['image_additional_data'] = $existingData['image'];
                unset($existingData['image']);
            }
            $existingData['id'] = $existingCategory->getId();
            $existingData['parent_id'] = $existingCategory->getParentId();
            $existingData['path'] = $existingCategory->getPath();
            $existingData['is_active'] = $existingCategory->getIsActive();
            $existingData['include_in_menu'] =
                isset($existingData['include_in_menu']) ? (bool)$existingData['include_in_menu'] : false;
            $category->addData($existingData);
        } else {
            $parentId = $category->getParentId() ?: $this->storeManager->getStore()->getRootCategoryId();
            $parentCategory = $this->get($parentId);
            /** @var  $category Category */
            $category->setPath($parentCategory->getPath());
        }
        try {
            $this->validateCategory($category);
            $this->categoryResource->save($category);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save category: %message', ['message' => $e->getMessage()], $e);
        }
        return $category->getId();
    }

    /**
     * Get info about category by category id
     *
     * TODO: MAGETWO-30203 $storeId is temporary solution
     *
     * @param int $categoryId
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function get($categoryId, $storeId = null)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        if (null !== $storeId) {
            $category->setStoreId($storeId);
        }
        $this->categoryResource->load($category, $categoryId);

        if (!$category->getId()) {
            throw NoSuchEntityException::singleField('id', $categoryId);
        }
        return $category;
    }

    /**
     * Delete category by identifier
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category category which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
     * Delete category by identifier
     *
     * @param int $categoryId
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
