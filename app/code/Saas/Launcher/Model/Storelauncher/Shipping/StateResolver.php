<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * State resolver for Shipping Tile
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Shipping_StateResolver
    extends Saas_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
{
    /**
     * Flag that shows if configuration check is required to identify tile state
     *
     * @var bool
     */
    protected $_isConfigRequired;

    /**
     * @param Magento_Core_Model_App $app
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Core_Model_App $app,
        Magento_Core_Controller_Request_Http $request
    ) {
        parent::__construct($app);
        // shipping tile can be considered complete when user simply deselects 'Shipping Enabled' checkbox
        $isShippingEnabled = $request->getPost('shipping_enabled');
        $this->_isConfigRequired = !empty($isShippingEnabled);
    }

    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        if (!$this->_isConfigRequired) {
            return true;
        }

        return $this->isShippingConfigured();
    }

    /**
     * Check whether at least one of main shipping methods has been configured
     *
     * @return boolean
     */
    public function isShippingConfigured()
    {
        $shippingConfigPaths = $this->_getRelatedShippingMethods();
        $currentStore = $this->_app->getStore();
        // the Shipping is considered to be configured if at least one of the related shipping methods is active
        foreach ($shippingConfigPaths as $configPath) {
            if ((bool)$currentStore->getConfig($configPath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve a list of shipping method ID => path pairs related to the 'Shipping' tile
     *
     * @return array
     */
    protected function _getRelatedShippingMethods()
    {
        return array(
            'flatrate' => 'carriers/flatrate/active',
            'ups' => 'carriers/ups/active',
            'usps' => 'carriers/usps/active',
            'fedex' => 'carriers/fedex/active',
            'dhlint' => 'carriers/dhlint/active',
        );
    }

    /**
     * Get a list of configured Shipping methods
     *
     * @return array
     */
    public function getConfiguredShippingMethods()
    {
        $shippingConfigPaths = $this->_getRelatedShippingMethods();
        $currentStore = $this->_app->getStore();
        $configuredMethods = array();
        foreach ($shippingConfigPaths as $methodName => $configPath) {
            if ((bool)$currentStore->getConfig($configPath)) {
                $configuredMethods[$methodName] = $currentStore->getConfig('carriers/' . $methodName . '/title');
            }
        }
        return $configuredMethods;
    }
}
