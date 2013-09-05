<?php
    /**
     * {license_notice}
     *
     * @copyright   {copyright}
     * @license     {license_link}
     */
class Magento_AdminGws_Model_Plugin_CatalogCategory extends Magento_AdminGws_Model_Observer_Abstract
{
    public function aroundIsAllowedCategory(array $arguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        $category = reset($arguments);
        $isAllowed = $invocationChain->proceed($arguments);

        if ($this->_role->getIsAll()) {
            return $isAllowed;
        }
        if (!$this->_role->hasExclusiveCategoryAccess($category->getPath())) {
            return false;
        }
        return $isAllowed;
    }
}