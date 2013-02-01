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
 * Bussines Info Tile tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_StoreLauncher_StoreInfo_TileTest extends Mage_Selenium_TestCase
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
     * <p>Store Info tile is displayed on the Store Launcher page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6502
     */
    public function storeInfoTileIsDisplayedOnTheStoreLauncherPage()
    {
        $this->assertTrue($this->controlIsPresent('fieldset', 'bussines_info_tile'),
            'Bussines Info tile is absent on Store Launcher page');
    }

    /**
     * <p>Design of tile is changed after mouse navigation</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6503
     */
    public function designOfTileIsChangedAfterMouseNavigation()
    {
        $this->markTestSkipped('TODO');
    }
}