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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_AdminMenu_AdminMenuTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Verify that  only one top level element has css class="active " when any  of child element is selected.
     *
     * Preconditions:
     * 1. The dataset that describe all menu items  is presented ( it should be different fo ee and ce release).
     * Steps to reproduce:
     * 1. Navigate menu according to dataset.
     * 2. Verify that all pages that described in dataset is presented.
     * 3. Verify that only one top level element(element menu parent level0) has [ <li class = "active parent level0"  ..]  and they HTML tag A  = [<a class="active" ..]
     *
     * @test
     * @param string $currentPageName
     *
     * @dataProvider menuItemsWithParentsDataProvider
     * @TestlinkId TL-MAGE-5674
     */
    public function testCurrentMenuItemIsHighlighted($currentPageName)
    {
        $this->navigate($currentPageName);

        $area = $this->_configHelper->getArea();
        $mca = $this->_uimapHelper->getPageMca($area, $currentPageName);
        $this->addParameter('mcaXpath', $mca);
        $xPath = $this->_getControlXpath('pageelement', 'general_menu_xpath');
        $this->assertTrue($this->isElementPresent($xPath),
            "Expected menu item for page $currentPageName ($xPath) doesn't exist");
        $countXpath = $this->_getControlXpath('pageelement', 'active_menu_element');
        $this->assertCount(1, $this->getElementsByXpath($countXpath));
    }

    /**
     * Retrieve menu items with their top level parents
     */
    public function menuItemsWithParentsDataProvider()
    {
        $items = array();
        $menuArray = $this->loadDataSet('MenuElements', 'menu');
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($menuArray)) as $currentPageName => $value) {
            $items[] = array($currentPageName);
        }
        return $items;
    }
}