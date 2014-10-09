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

class CategoryManager implements \Magento\Catalog\Api\CategoryManagementInterface
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function move($categoryId, $parentId, $afterId = null)
    {
        $model = $this->loadCategory($categoryId);
        $parentCategory = $this->loadCategory($parentId);

        if ($parentCategory->hasChildren()) {
            $parentChildren = $parentCategory->getChildren();
            $categoryIds = explode(',', $parentChildren);
            $lastId = array_pop($categoryIds);
            $afterId = (is_null($afterId) || $afterId > $lastId) ? $lastId : $afterId;
        }

        if (strpos($parentCategory->getPath(), $model->getPath()) === 0) {
            throw new \Magento\Framework\Model\Exception(
                "Operation do not allow to move a parent category to any of children category"
            );
        }
        try {
            $model->move($parentId, $afterId);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Model\Exception('Could not move category');
        }
        return true;
    }

    /**
     * Load category
     *
     * @param int $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Model\Category
     */
    protected function loadCategory($id)
    {
        $model = $this->categoryFactory->create();
        $model->load($id);
        if (!$model->getId()) {
            throw NoSuchEntityException::singleField('id', $id);
        }
        return $model;
    }
}
