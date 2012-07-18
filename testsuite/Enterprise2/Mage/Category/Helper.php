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
            switch ($tab) {
                case 'category_permissions':
                    if (!isset($categoryData['category_permissions'])) {
                        break;
                    }
                    $this->openTab('category_permissions_tab');
                    $count = 1;
                    foreach ($categoryData['category_permissions'] as $permission) {
                        $this->clickButton('new_permission', false);
                        $this->addParameter('row', $count);
                        $this->fillFieldset($permission, 'category_permissions');
                        $count++;
                    }
                    break;
                case 'category_products':
                    $arrayKey = $tab . '_data';
                    if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                        $this->openTab($tab);
                        foreach ($categoryData[$arrayKey] as $value) {
                            $this->productHelper()->assignProduct($value, $tab);
                        }
                    }
                    break;
                default:
                    $this->fillForm($categoryData, $tab);
                    break;
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
        $this->categoryHelper()->selectCategory($categoryPath);
        $this->openTab('category_permissions_tab');
        while ($this->buttonIsPresent('delete_all_permissions_visible')) {
            $this->clickButton('delete_all_permissions_visible', false);
        }
        $this->clickButton('save_category', false);
        $this->pleaseWait();
    }
}
