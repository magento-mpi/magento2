<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Shipping_StateResolverTest
    extends Mage_Launcher_Model_Tile_StateResolver_ConfigBased_TestCaseAbstract
{
    /**
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Config $config
     * @return Mage_Launcher_Model_Storelauncher_Shipping_StateResolver
     */
    protected function _getStateResolverInstance(Mage_Core_Model_App $app, Mage_Core_Model_Config $config)
    {
        return new Mage_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $config);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function isTileCompleteDataProvider()
    {
        // Shipping tile is considered to be complete when at least one of the related shipping methods is active
        return array(
            array(
                array(
                    'carriers/flatrate/active' => 1,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 1,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 1,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 1,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 1,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                false,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 1,
                    'carriers/ups/active' => 1,
                    'carriers/usps/active' => 1,
                    'carriers/fedex/active' => 1,
                    'carriers/dhlint/active' => 1,
                ),
                true,
            ),
        );
    }
}
