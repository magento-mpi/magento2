<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Plugin_CategoryResource
{
    /**
     * @var Magento_AdminGws_Model_Role
     */
    protected $_role;

    /**
     * @param Magento_AdminGws_Model_Role $role
     */
    public function __construct(Magento_AdminGws_Model_Role $role)
    {
        $this->_role = $role;
    }

    /**
     * Check if category can be moved
     *
     * @param array $methodArguments
     * @return array
     * @throws Magento_Core_Exception
     */
    public function beforeChangeParent(array $methodArguments)
    {
        if ($this->_role->getIsAll()) {
            return $methodArguments;
        }

        /** @var $currentCategory Magento_Catalog_Model_Category */
        /** @var $parentCategory Magento_Catalog_Model_Category */
        list($currentCategory, $parentCategory,) = $methodArguments;
        /** @var $category Magento_Catalog_Model_Category */
        foreach (array($parentCategory, $currentCategory) as $category) {
            if (!$this->_role->hasExclusiveCategoryAccess($category->getData('path'))) {
                throw new Magento_Core_Exception(__('You need more permissions to save this item.'));
            }
        }
        return $methodArguments;
    }
}

