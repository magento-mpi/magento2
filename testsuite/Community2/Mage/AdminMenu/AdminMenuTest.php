<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminMenu
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
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
     * 3. Verify that only one top level element(element menu parent level0)
     *    has [ <li class = "active parent level0"  ..]  and they HTML tag A  = [<a class="active" ..]
     *
     * @param string $currentPageName
     *
     * @test
     * @dataProvider menuItemsWithParentsDataProvider
     * @TestlinkId TL-MAGE-5674
     */
    public function testCurrentMenuItemIsHighlighted($currentPageName)
    {
        $this->navigate($currentPageName);

        $area = $this->getConfigHelper()->getArea();
        $mca = $this->getUimapHelper()->getPageMca($area, $currentPageName);
        $this->addParameter('mcaXpath', $mca);
        $this->assertTrue($this->controlIsPresent('pageelement', 'general_menu_xpath'),
            "Expected menu item for page $currentPageName doesn't exist");
        $this->assertEquals(1, $this->getControlCount('pageelement', 'active_menu_element'));
    }

    /**
     * Retrieve menu items with their top level parents
     */
    public function menuItemsWithParentsDataProvider()
    {
        $items = array();
        $menuArray = $this->loadDataSet('MenuElements', 'menu');
        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($menuArray)) as $currentPageName => $value) {
            $items[] = array($currentPageName);
        }
        return $items;
    }
}