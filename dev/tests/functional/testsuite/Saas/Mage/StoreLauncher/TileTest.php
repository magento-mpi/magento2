<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common Tile tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_StoreLauncher_TileTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Tile is displayed on the Store Launcher page</p>
     *
     * @param string $tile Tile code
     * @test
     * @dataProvider tileNamesDataProvider
     * @TestlinkId TL-MAGE-6502
     */
    public function tileIsDisplayed($tile)
    {
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_FIELDSET, $tile),
            'Tile is absent on Store Launcher page. Tile code: ' . $tile);
    }

    /**
     * <p>Design of tile is changed after mouse navigation</p>
     *
     * @param string $tile Tile code
     * @test
     * @dataProvider tileNamesDataProvider
     * @TestlinkId TL-MAGE-6503
     */
    public function designOfTileIsChangedAfterMouseNavigation($tile)
    {
        /**
         * @var Saas_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileElement
         */
        $this->moveto($this->getElement($this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'page_title')));
        $tileXpath = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, $tile);
        $tileElement = $this->getElement($tileXpath);
        $style = $helper->getTileBgColor($tileElement);
        $this->assertNotEmpty($style, 'Could not get Tile style');
        //Mouse over
        $this->moveto($tileElement);
        $mouseOverStyle = $helper->getTileBgColor($tileElement);
        $this->assertNotEmpty($mouseOverStyle, 'Could not get Tile style');
        $this->assertNotEquals($style, $mouseOverStyle, 'Style is not changed on mouse over');
        //Tile can be selected
        $this->refresh();
        $tileElement = $this->getElement($tileXpath);
        $tileElement->click();
        $tileSelectedStyle = $helper->getTileBgColor($tileElement);
        $this->assertNotEmpty($tileSelectedStyle, 'Could not get Tile style');
        $this->assertNotEquals($style, $tileSelectedStyle, 'Style is not changed after mouse click');
    }

    /**
     * Data for drawerIsDisplayed|returnToTheStoreLauncherPage
     *
     * @return array
     */
    public function tileNamesDataProvider()
    {
        return array(
            array('bussines_info_tile'),
            array('tax_rules_tile'),
            array('payment_tile'),
            array('product_tile'), //TL-MAGE-6819, TL-MAGE-6820
            array('shipping_tile')
        );
    }
}