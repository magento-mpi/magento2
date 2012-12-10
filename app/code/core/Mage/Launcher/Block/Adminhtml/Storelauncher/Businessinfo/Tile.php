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
 * BusinessInfo Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Region Model
     *
     * @var Mage_Directory_Model_Region
     */
    protected $_regionModel;

    /**
     * Template for Businessinfo Tile Block
     *
     * @var string
     */
    protected $_template = 'page/storelauncher/tile/businessinfo.phtml';

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Directory_Model_Config_Source_Country $countryModel
     * @param Mage_Directory_Model_Region $regionModel
     * @param Mage_Launcher_Helper_Data $dataHelper
     * @param Mage_Directory_Helper_Data $regionHelper
     * @param Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory $validateVat
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Directory_Model_Region $regionModel,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_regionModel = $regionModel;
    }

    /**
     * Prepare Address Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareAddressData($data)
    {
        $groups = $data['groups'];
        $data['region_id'] = isset($data['region_id']) ? $data['region_id'] : 0;
        $region = $this->_regionModel->load($data['region_id'])->getName();
        $groups['general']['store_information']['fields']['address']['value'] =
            sprintf("%s\n%s\n%s\n%s\n%s",
                $data['street_line1'],
                $data['street_line2'],
                $data['city'],
                $data['postcode'],
                $region
            );

        if (isset($data['use_for_shipping'])) {
            $storeInformation = $groups['general']['store_information']['fields'];
            $shipping = array(
                'country_id' => $storeInformation['merchant_country'],
                'region_id' => array('value' => $data['region_id']),
                'postcode' => array('value' => $data['postcode']),
                'city' => array('value' => $data['city']),
                'street_line1' => array('value' => $data['street_line1']),
                'street_line2' => array('value' => $data['street_line2'])
            );
            $groups['shipping']['origin']['fields'] = $shipping;
        }
        return $groups;
    }

    /**
     * Get Address
     *
     * @return string
     */
    public function getAddress()
    {
        $address = $this->_storeConfig->getConfig('general/store_information/address');
        $addressValues = explode("\n", $address);
        $email = $this->_storeConfig->getConfig('trans_email/ident_general/email');
        $addressValues[] = $email;
        return $addressValues;
    }
}
