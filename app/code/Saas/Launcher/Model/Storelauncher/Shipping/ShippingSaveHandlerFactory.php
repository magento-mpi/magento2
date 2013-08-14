<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipping configuration save handler factory
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerFactoryAbstract
{
    /**
     * Retrieve save handler ID - save handler class name map
     *
     * @return array
     */
    public function getSaveHandlerMap()
    {
        return array(
            'carriers_flatrate' => 'Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FlatrateSaveHandler',
            'carriers_ups' => 'Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UpsSaveHandler',
            'carriers_usps' => 'Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_UspsSaveHandler',
            'carriers_fedex' => 'Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_FedexSaveHandler',
            'carriers_dhlint' => 'Saas_Launcher_Model_Storelauncher_Shipping_Savehandlers_DhlSaveHandler',
        );
    }
}
