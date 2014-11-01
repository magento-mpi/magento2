<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Catalog\Model\CategoryFactory;
use Magento\catalog\Service\V1\Data\Category as CategoryDataObject;

class Mapper
{
    /** @var  CategoryFactory */
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
     * @param  CategoryDataObject $category
     * @param  \Magento\Catalog\Model\Category $categoryModel
     * @return \Magento\Catalog\Model\Category
     * @throws \RuntimeException
     */
    public function toModel(
        CategoryDataObject $category,
        \Magento\Catalog\Model\Category $categoryModel = null
    ) {
        $categoryModel = $categoryModel ?: $this->categoryFactory->create();
        $data = ExtensibleDataObjectConverter::toFlatArray($category);
        /** @see /app/code/Magento/Catalog/Controller/Adminhtml/Category.php method "_filterCategoryPostData" */
        if (isset($data['image']) && is_array($data['image'])) {
            $data['image_additional_data'] = $data['image'];
            unset($data['image']);
        }
        // this fields should not be changed
        $data[CategoryDataObject::ID]   = $categoryModel->getId();
        $data[CategoryDataObject::PARENT_ID]   = $categoryModel->getParentId();
        $data[CategoryDataObject::PATH]   = $categoryModel->getPath();

        /** fill required fields */
        $data['is_active'] = $category->isActive();
        $data['include_in_menu'] = isset($data['include_in_menu']) ? (bool)$data['include_in_menu'] : false;

        $categoryModel->addData($data);

        if (!is_numeric($categoryModel->getAttributeSetId())) {
            $categoryModel->setAttributeSetId($categoryModel->getDefaultAttributeSetId());
        }
        return $categoryModel;
    }
}
