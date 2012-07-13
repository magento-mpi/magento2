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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        parent::fillCategoryInfo($categoryData);
        if (isset($categoryData['category_permissions'])) {
            $this->openTab('category_permissions_tab');
            $count = 1;
            foreach ($categoryData['category_permissions'] as $permission) {
                $this->clickButton('new_permission', false);
                $this->waitForAjax();
                $this->addParameter('row', $count);
                $this->fillFieldset($permission, 'category_permissions');
                $count++;
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
        $xpath = $this->_getControlXpath('button', 'delete_all_permissions_visible');
        $count = $this->getXpathCount($xpath);
        for ($i=0; $i < $count; $i++) {
            $this->clickButton('delete_all_permissions_visible', false);
            $this->waitForAjax();
        }
//        while ($this->controlIsPresent('pageelement', 'option_box')) {
//            $this->clickButton('delete_all_permissions', false);
//            $this->waitForAjax();
//            $this->clickButton('save_category');
//        }
    }

    /**
     * Set Category Permissions for Category for existing category
     *
     * @param array $permissionsData
     * @param string $categoryPath
     */
    public function setPermissions($permissionsData, $categoryPath)
    {
        $this->categoryHelper()->selectCategory($categoryPath);
        $this->openTab('category_permissions_tab');
        $count = 1;
        foreach ($permissionsData as $permission) {
            $this->clickButton('new_permission', false);
            $this->waitForAjax();
            $this->addParameter('row', $count);
            $this->fillFieldset($permission, 'category_permissions');
            $count++;
        }
        $this->clickButton('save_category');
    }
}
