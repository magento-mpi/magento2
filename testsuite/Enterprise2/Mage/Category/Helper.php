<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  helper
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
                case 'category_permissions_tab':
                    if (!isset($categoryData['category_permissions'])) {
                        break;
                    }
                    $this->openTab('category_permissions_tab');
                    $xpath = $this->_getControlXpath('pageelement', 'option_box');
                    foreach ($categoryData['category_permissions'] as $permission) {
                        $count = $this->getXpathCount($xpath);
                        $row = $count + 1;
                        $this->addParameter('row', $row);
                        $this->clickButton('new_permission', false);
                        $this->waitForAjax();
                        if (!$this->controlIsPresent('dropdown', 'website')){
                            unset ($permission['website']);
                        }
                        $this->fillFieldset($permission, 'category_permissions');
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
