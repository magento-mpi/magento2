<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

use Magento\Framework\Service\EavDataObjectConverter;
use Magento\Catalog\Model\CategoryFactory;
use Magento\catalog\Service\V1\Data\Category as CategoryDataObject;

class Mapper
{
    /** @var  CategoryFactory */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param  CategoryDataObject $category
     * @param  \Magento\Catalog\Model\Category $categoryModel
     * @return \Magento\Catalog\Model\Category
     * @throws \RuntimeException
     */
    public function toModel(
        CategoryDataObject $category,
        \Magento\Catalog\Model\Category $categoryModel = null
    ) {
        $categoryModel = $this->categoryFactory->create();
        $data = EavDataObjectConverter::toFlatArray($category);
        /** @see /app/code/Magento/Catalog/Controller/Adminhtml/Category.php method "_filterCategoryPostData" */
        if (isset($data['image']) && is_array($data['image'])) {
            $data['image_additional_data'] = $data['image'];
            unset($data['image']);
        }
        $categoryModel->addData($data);

        $parentId = $category->getParentId() ?: $this->storeManager->getStore()->getRootCategoryId();
        $parentCategory = $this->categoryFactory->create()->load($parentId);
        $categoryModel->setPath($parentCategory->getPath());

        if (!is_numeric($categoryModel->getAttributeSetId())) {
            $categoryModel->setAttributeSetId($categoryModel->getDefaultAttributeSetId());
        }
        return $categoryModel;
    }
}
