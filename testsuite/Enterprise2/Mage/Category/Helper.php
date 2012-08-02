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
                case 'category_permissions_tab':
                    if (!isset($categoryData['category_permissions'])) {
                        break;
                    }
                    $this->openTab('category_permissions_tab');
                    $this->addNewCategoryPermissions($categoryData['category_permissions']);
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
        $xpath = $this->_getControlXpath('fieldset', 'new_category_permission');
        foreach ($permissions as $permission) {
            $count = $this->getXpathCount($xpath);
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

    /**
     * Find category with valid name
     *
     * @param string $catName
     * @param null|string $parentCategoryId
     * @param string $fieldsetName
     *
     * @return array
     */
    public function defineCorrectCategory($catName, $parentCategoryId = null, $fieldsetName = 'select_category')
    {
        $isCorrectName = array();
        $categoryText = '/div/a/span';

        if (!$parentCategoryId) {
            $this->addParameter('rootName', $catName);
            $catXpath =
                $this->_getControlXpath('link', 'root_category', $this->_findUimapElement('fieldset', $fieldsetName));
        } else {
            $this->addParameter('parentCategoryId', $parentCategoryId);
            $this->addParameter('subName', $catName);
            $isDiscloseCategory =
                $this->_getControlXpath('link', 'expand_category', $this->_findUimapElement('fieldset', $fieldsetName));
            $catXpath =
                $this->_getControlXpath('link', 'sub_category', $this->_findUimapElement('fieldset', $fieldsetName));
            if ($this->isElementPresent($isDiscloseCategory)) {
                $this->click($isDiscloseCategory);
                $this->pleaseWait();
            }
        }
        $this->waitForAjax();

        $text = $this->getText($catXpath . '[1]' . $categoryText);
        $text = preg_replace('/ \([0-9]+\)/', '', $text);
        if ($catName === $text) {
            $isCorrectName[] = $this->getAttribute($catXpath . '[1]' . '/div/a/@id');
        }

        return $isCorrectName;
    }
} 
