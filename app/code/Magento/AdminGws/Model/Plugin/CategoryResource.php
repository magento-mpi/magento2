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
     * @param array $methodArguments
     * @return array
     * @throws \Magento\Core\Exception
     */
    public function beforeChangeParent(array $methodArguments)
    {
        if ($this->_role->getIsAll()) {
            return $methodArguments;
        }

        /** @var $currentCategory \Magento\Catalog\Model\Category */
        /** @var $parentCategory \Magento\Catalog\Model\Category */
        list($currentCategory, $parentCategory,) = $methodArguments;
        /** @var $category \Magento\Catalog\Model\Category */
        foreach (array($parentCategory, $currentCategory) as $category) {
            if (!$this->_role->hasExclusiveCategoryAccess($category->getData('path'))) {
                throw new \Magento\Core\Exception(__('You need more permissions to save this item.'));
            }
        }
        return $methodArguments;
    }
}

