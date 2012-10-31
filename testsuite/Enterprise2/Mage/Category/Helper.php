<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Category_Helper extends Core_Mage_Category_Helper
{
    /**
     * Fill in Category information
     *
     * @param array $categoryData
     */
    public function fillCategoryInfo(array $categoryData)
    {
        $tabs = $this->getCurrentUimapPage()->getAllTabs();
        foreach ($tabs as $tab => $values) {
            if (!$this->controlIsPresent('tab', $tab)) {
                continue;
            }
            if ($tab != 'category_products' && $tab != 'category_permissions_tab') {
                $this->fillTab($categoryData, $tab, false);
                continue;
            }
            if ($tab == 'category_permissions_tab' && isset($categoryData['category_permissions'])) {
                $this->openTab('category_permissions_tab');
                $this->addNewCategoryPermissions($categoryData['category_permissions']);
                continue;
            }
            $arrayKey = $tab . '_data';
            if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                $this->openTab($tab);
                foreach ($categoryData[$arrayKey] as $value) {
                    $this->productHelper()->assignProduct($value, $tab);
                }
            }
        }
    }

    /**
     * Delete Category Permissions
     *
     * @param string $categoryPath
     */
    public function deleteAllPermissions($categoryPath)
    {
        $this->selectCategory($categoryPath);
        $this->openTab('category_permissions_tab');
        while ($this->buttonIsPresent('delete_all_permissions_visible')) {
            $this->clickButton('delete_all_permissions_visible', false);
        }
    }

    /**
     * @param array $permissions
     */
    public function addNewCategoryPermissions($permissions = array())
    {
        foreach ($permissions as $permission) {
            $count = count($this->getControlElements('fieldset', 'new_category_permission'));
            $this->addParameter('row', $count + 1);
            $this->clickButton('new_permission', false);
            $this->waitForElement($this->_getControlXpath('button', 'delete_permissions'));
            if (!empty($permission)) {
                if (isset($permission['website']) && !$this->controlIsPresent('dropdown', 'website')) {
                    unset ($permission['website']);
                }
                $this->fillFieldset($permission, 'new_category_permission');
            }
        }
    }
}
