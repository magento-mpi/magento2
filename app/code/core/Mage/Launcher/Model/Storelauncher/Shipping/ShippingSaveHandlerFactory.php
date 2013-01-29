<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping configuration save handler factory
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory
    extends Mage_Launcher_Model_Tile_ConfigBased_ConfigDataSaveHandlerFactoryAbstract
{
    /**
     * Retrieve save handler ID - save handler class name map
     *
     * @return array
     */
    public function getSaveHandlerMap()
    {
        return array(
            'carriers_flatrate' => 'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler',
            'carriers_ups' => 'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler',
            'carriers_usps' => 'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_UspsSaveHandler',
            'carriers_fedex' => 'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_FedexSaveHandler',
            'carriers_dhlint' => 'Mage_Launcher_Model_Storelauncher_Shipping_Savehandlers_DhlSaveHandler',
        );
    }
}
