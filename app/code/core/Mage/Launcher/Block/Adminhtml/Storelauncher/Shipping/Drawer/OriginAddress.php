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
 * Shipping Origin Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddress
    extends Mage_Backend_Block_Widget_Form
{
    /**
     * Countries
     *
     * @var Mage_Directory_Model_Config_Source_Country
     */
    protected $_countryModel;

    /**
     * Regions
     *
     * @var Mage_Directory_Model_Region
     */
    protected $_regionModel;

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
     * @param Mage_Directory_Model_Config_Source_Country $countryModel
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
        Mage_Directory_Model_Config_Source_Country $countryModel,
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
     * Prepare Shipping Origin form
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $addressData = $this->getAddressData();

        $form = new Varien_Data_Form(array(
            'method' => 'post',
            'id' => 'shipping-origin-form'
        ));

        $helper = $this->helper('Mage_Launcher_Helper_Data');

        $fieldset = $form->addFieldset('origin_address_fieldset', array());
        $fieldset->addField('street_line1', 'text', array(
            'name' => 'street_line1',
            'label' => $helper->__('Street Address 1'),
            'required' => false,
            'value' => $addressData['street_line1']
        ));

        $fieldset->addField('street_line2', 'text', array(
            'name' => 'street_line2',
            'label' => $helper->__('Street Address 2'),
            'required' => false,
            'value' => $addressData['street_line2']
        ));

        $fieldset->addField('city', 'text', array(
            'name' => 'city',
            'label' => $helper->__('City'),
            'required' => false,
            'value' => $addressData['city']
        ));

        $fieldset->addField('postcode', 'text', array(
            'name' => 'postcode',
            'label' => $helper->__('ZIP/Postal Code'),
            'required' => false,
            'value' => $addressData['postcode']
        ));

        $countries = $this->_countryModel->toOptionArray();
        $fieldset->addField('country_id', 'select', array(
            'name' => 'country_id',
            'label' => $helper->__('Country'),
            'required' => true,
            'values' => $countries,
            'class' => 'countries',
            'value' => $addressData['country_id'],
            'after_element_html' => '<script type="text/javascript">'
                . 'originAddress = new originModel();'
                . '</script>',
        ));
        $countryId = isset($addressData['country_id']) ? $addressData['country_id'] : 'US';
        $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($countryId);
        $regions = $regionCollection->toOptionArray();
        if (!empty($regions)) {
            $fieldset->addField('region_id', 'select', array(
               'name' => 'region_id',
               'label' => $helper->__('State/Region'),
               'values' => $regions,
               'value' => $addressData['region_id'],
            ));
        } else {
            $fieldset->addField('region_id', 'text', array(
                 'name' => 'region_id',
                 'label' => $helper->__('State/Region'),
                 'value' => $addressData['region_id']
            ));
        }

        $form->setUseContainer(false);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get Shipping Origin Address data from system configuration
     *
     * @return array
     */
    public function getAddressData()
    {
        $addressData = array();
        $addressData['street_line1'] = $this->_storeConfig->getConfig('shipping/origin/street_line1');
        $addressData['street_line2'] = $this->_storeConfig->getConfig('shipping/origin/street_line2');
        $addressData['city'] = $this->_storeConfig->getConfig('shipping/origin/city');
        $addressData['postcode'] = $this->_storeConfig->getConfig('shipping/origin/postcode');
        $addressData['country_id'] = $this->_storeConfig->getConfig('shipping/origin/country_id');
        $addressData['region_id'] = $this->_storeConfig->getConfig('shipping/origin/region_id');

        return $addressData;
    }

}