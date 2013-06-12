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

        $area = $this->getConfigHelper()->getArea();
        $mca = $this->getUimapHelper()->getPageMca($area, $currentPageName);
        $this->addParameter('mcaXpath', $mca);
        $this->assertEquals(1, $this->getControlCount('pageelement', 'active_menu_element'),
            "Top level menu item for '$currentPageName' isn't active");
        $this->assertTrue($this->controlIsPresent('pageelement', 'general_menu_xpath'),
            "Expected active menu item for page $currentPageName doesn't exist");
    }

    /**
     * Retrieve menu items with their top level parents
     *
     * @return array $items
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
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