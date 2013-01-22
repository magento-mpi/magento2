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
     * Country Model
     *
     * @var Mage_Directory_Model_Country
     */
    protected $_countryModel;

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
     * @param Magento_Filesystem $filesystem
     * @param Mage_Directory_Model_Country $countryModel
     * @param Mage_Directory_Model_Region $regionModel
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
        Magento_Filesystem $filesystem,
        Mage_Directory_Model_Country $countryModel,
        Mage_Directory_Model_Region $regionModel,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $filesystem, $data
        );
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
    }

    /**
     * Get Address
     *
     * @return string
     */
    public function getAddress()
    {
        $addressValues = array();
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/street_line1');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/street_line2');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/city');
        $addressValues[] = $this->_storeConfig->getConfig('general/store_information/postcode');
        $countryCode = $this->_storeConfig->getConfig('general/store_information/country_id');
        $countryName = $this->_countryModel->loadByCode($countryCode)->getName();

        $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($countryCode);
        $regions = $regionCollection->toOptionArray();
        $regionName = $this->_storeConfig->getConfig('general/store_information/region_id');
        if (!empty($regions)) {
            $this->_regionModel->load($regionName);
            $regionName = $this->_regionModel->getName();
        }
        $addressValues[] = $regionName;
        $addressValues[] = $countryName;
        $addressValues[] = $this->_storeConfig->getConfig('trans_email/ident_general/email');
        return $addressValues;
    }
}
