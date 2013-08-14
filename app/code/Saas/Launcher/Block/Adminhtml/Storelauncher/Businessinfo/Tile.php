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
 * BusinessInfo Tile Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Tile extends Saas_Launcher_Block_Adminhtml_Tile
{
    /**
     * Region Model
     *
     * @var Magento_Directory_Model_Region
     */
    protected $_regionModel;

    /**
     * Country Model
     *
     * @var Magento_Directory_Model_Country
     */
    protected $_countryModel;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Directory_Model_Country $countryModel
     * @param Magento_Directory_Model_Region $regionModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Directory_Model_Country $countryModel,
        Magento_Directory_Model_Region $regionModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
    }

    /**
     * Retrieve Business Address
     *
     * @return array
     */
    public function getAddress()
    {
        $addressValues = array();
        $addressValues['address-street-line1'] = $this->_storeConfig
            ->getConfig('general/store_information/street_line1');
        $addressValues['address-street-line2'] = $this->_storeConfig
            ->getConfig('general/store_information/street_line2');
        $addressValues['address-city'] = $this->_storeConfig->getConfig('general/store_information/city');
        $addressValues['address-postcode'] = $this->_storeConfig->getConfig('general/store_information/postcode');
        $countryCode = $this->_storeConfig->getConfig('general/store_information/country_id');
        if (!empty($countryCode)) {
            $countryName = $this->_countryModel->loadByCode($countryCode)->getName();

            $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($countryCode);
            $regions = $regionCollection->toOptionArray();
            $regionName = $this->_storeConfig->getConfig('general/store_information/region_id');
            if (!empty($regions)) {
                $this->_regionModel->load($regionName);
                $regionName = $this->_regionModel->getName();
            }
            $addressValues['address-region-name'] = $regionName;
            $addressValues['address-country-name'] = $countryName;
        }
        return $addressValues;
    }

    /**
     * Retrieve store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return (string)$this->_storeConfig->getConfig('general/store_information/name');
    }

    /**
     * Retrieve general email address
     *
     * @return string
     */
    public function getGeneralEmail()
    {
        return (string)$this->_storeConfig->getConfig('trans_email/ident_general/email');
    }

    /**
     * Check Business Address is configured
     *
     * @return boolean
     */
    public function isBusinessAddressConfigured()
    {
        $addressValues = $this->getAddress();
        if (!empty($addressValues['address-street-line1'])
            && !empty($addressValues['address-city'])
            && !empty($addressValues['address-country-name'])
        ) {
            return true;
        }
        return false;
    }
}
