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
 * Tax Rules Tile tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_StoreLauncher_TaxRules_TileTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('store_launcher');
    }

    /**
     * <p>Tax Rules tile is displayed on the Store Launcher page</p>
     *
     * @test
     */
    public function taxRulesTileIsDisplayedOnTheStoreLauncherPage()
    {
        $this->assertTrue($this->controlIsPresent('fieldset', 'tax_rules_tile'),
            'Tax Rules tile is absent on Store Launcher page');
    }

    /**
     * <p>Design of tile is changed after mouse navigation</p>
     *
     * @test
     */
    public function designOfTileIsChangedAfterMouseNavigation()
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $tileElement
         */
        $this->moveto($this->getElement($this->_getControlXpath('pageelement', 'page_title')));
        $tileXpath = $this->_getControlXpath('fieldset', 'tax_rules_tile');
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
}