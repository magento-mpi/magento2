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
 * Save handler for Shipping Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Shipping_SaveHandler extends Mage_Launcher_Model_Tile_MinimalSaveHandler
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Backend_Model_Config
     */
    protected $_backendConfigModel;

    /**
     * Shipping information save handler factory
     *
     * @var Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory
     */
    protected $_saveHandlerFactory;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @param Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory $saveHandlerFactory
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel,
        Mage_Launcher_Model_Storelauncher_Shipping_ShippingSaveHandlerFactory $saveHandlerFactory
    ) {
        $this->_config = $config;
        $this->_backendConfigModel = $backendConfigModel;
        $this->_saveHandlerFactory = $saveHandlerFactory;
    }

    /**
     * Save data related to shipping method
     *
     * @param array $data request data
     * @throws Mage_Launcher_Exception
     */
    public function saveShippingMethod(array $data)
    {
        $shippingMethodId = isset($data['shipping_method']) ? (string)$data['shipping_method'] : null;
        if (!in_array($shippingMethodId, $this->getRelatedShippingMethods())) {
            throw new Mage_Launcher_Exception('Illegal shipping method ID specified.');
        }
        $this->_saveHandlerFactory->create($shippingMethodId)->save($data);
    }

    /**
     * Retrieve a list of shipping method IDs related to 'Shipping' tile
     *
     * @return array
     */
    public function getRelatedShippingMethods()
    {
        return array(
            'carriers_flatrate',
            'carriers_ups',
            'carriers_usps',
            'carriers_fedex',
            'carriers_dhlint',
        );
    }

    /**
     * Save origin shipping address data
     *
     * @param array $data request data
     */
    public function saveOriginAddress($data)
    {
        $this->_backendConfigModel->setSection('shipping')
            ->setGroups($this->prepareOriginAddressData($data))
            ->save();
        $this->_config->reinit();
    }

    /**
     * Prepare Origin Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareOriginAddressData($data)
    {
        $originAddressData['origin']['fields'] = array(
            'country_id' => array('value' => $data['country_id']),
            'region_id' => array('value' => $data['region_id']),
            'postcode' => array('value' => $data['postcode']),
            'city' => array('value' => $data['city']),
            'street_line1' => array('value' => $data['street_line1']),
            'street_line2' => array('value' => $data['street_line2'])
        );
        return $originAddressData;
    }
}
