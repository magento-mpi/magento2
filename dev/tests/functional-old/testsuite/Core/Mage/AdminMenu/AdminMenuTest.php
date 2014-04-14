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
class Core_Mage_AdminMenu_AdminMenuTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Verify that  only one top level element has css class="active " when any  of child element is selected.
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
        $this->assertEquals(
            1,
            $this->getControlCount('pageelement', 'active_menu_element'),
            "Top level menu item after navigate to '$currentPageName' page isn't active"
        );
        $this->addParameter('pageUrl', $this->url());
        $this->assertTrue(
            $this->controlIsPresent('pageelement', 'active_menu_with_page_link'),
            "Active top level menu item doesn't contain link for '$currentPageName' page"
        );
    }

    /**
     * Retrieve menu items with their top level parents
     *
     * @return array $items
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