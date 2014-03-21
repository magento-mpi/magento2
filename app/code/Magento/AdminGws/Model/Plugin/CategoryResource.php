<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model\Plugin;

class CategoryResource
{
    /**
     * Admin role
     *
     * @var \Magento\AdminGws\Model\Role
     */
    protected $_role;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     */
    public function __construct(\Magento\AdminGws\Model\Role $role)
    {
        $this->_role = $role;
    }

    /**
     * Check if category can be moved
     *
     * @param \Magento\Catalog\Model\Resource\Category $subject
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $newParent
     * @param null|int $afterCategoryId
     *
     * @return void
     * @throws \Magento\Core\Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeChangeParent(
        \Magento\Catalog\Model\Resource\Category $subject,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Model\Category $newParent,
        $afterCategoryId = null
    ) {
        if (!$this->_role->getIsAll()) {
            /** @var $categoryItem \Magento\Catalog\Model\Category */
            foreach (array($newParent, $category) as $categoryItem) {
                if (!$this->_role->hasExclusiveCategoryAccess($categoryItem->getData('path'))) {
                    throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
                }
            }
        }
    }
}
