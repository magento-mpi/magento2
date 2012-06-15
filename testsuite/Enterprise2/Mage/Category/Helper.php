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
        $page = $this->getCurrentUimapPage();
        $tabs = $page->getAllTabs();
        foreach ($tabs as $tab => $values)
        {
            if ($tab != 'category_products')
            {
                if ($tab == 'category_pemissions')
                {
                    if (($this->controlIsPresent('tab', 'category_pemissions')) && (isset($categoryData['customer_group'])))
                    {
                        $this->openTab($tab);
                        $this->clickButton('new_permission', false);
                        $this->addParameter('row','1');
                        if (($this->controlIsPresent('dropdown', 'website'))&& (isset($categoryData['website'])))
                        {
                            $this->fillFieldset(array('website' => $categoryData['website']),'category_permissions');
                        }
                        $this->fillFieldset(array('customer_group' => $categoryData['customer_group']),'category_permissions');
                        if (isset($categoryData['browsing_category']))
                        {
                            $this->addParameter('permissionCategory',$categoryData['browsing_category']);
                            $this->clickControl('field','browsing_category',false);
                        }
                        if (isset($categoryData['displaying_price']))
                        {
                            $this->addParameter('permissionPrice',$categoryData['displaying_price']);
                            $this->clickControl('field','displaying_price',false);
                        }
                        if (isset($categoryData['add_to_cart']))
                        {
                            $this->addParameter('permissionCart',$categoryData['add_to_cart']);
                            $this->clickControl('field','add_to_cart',false);
                        }
                    }
                }
                else
                    $this->fillForm($categoryData, $tab);
            }
            else
            {
                $arrayKey = $tab . '_data';
                $this->openTab($tab);
                if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                    foreach ($categoryData[$arrayKey] as $value) {
                        $this->productHelper()->assignProduct($value, $tab);
                    }
                }
            }
        }
    }
} 
