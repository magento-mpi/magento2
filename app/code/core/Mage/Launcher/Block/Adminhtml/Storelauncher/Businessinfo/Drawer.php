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
 * Businessinfo Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
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
     * Validate VAT Number
     *
     * @var Mage_Adminhtml_Block_Customer_System_Config_Validatevat
     */
    protected $_validateVatBlock;

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
        Mage_Directory_Model_Config_Source_Country $countryModel,
        Mage_Directory_Model_Region $regionModel,
        Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory $validateVat,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
        $this->_validateVatBlock = $validateVat->createVatValidator();
    }

    /**
     * Prepare Bussinessinfo drawer form
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $addressData = $this->getAddressData();

        $form = new Varien_Data_Form(array(
            'method' => 'post',
            'id' => 'drawer-form'
        ));

        $helper = $this->helper('Mage_Launcher_Helper_Data');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Store Info')));
        $fieldset->addField('store_name', 'text', array(
            'name' => 'groups[general][store_information][fields][name][value]',
            'label' => $helper->__('Store Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('general/store_information/name')
        ));

        $fieldset->addField('store_email', 'text', array(
            'name' => 'groups[trans_email][ident_general][fields][email][value]',
            'label' => $helper->__('Store Contact Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_general/email')
        ));

        $fieldset->addField('store_phone', 'text', array(
            'name' => 'groups[general][store_information][fields][phone][value]',
            'label' => $helper->__('Store Contact Phone Number'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('general/store_information/phone')
        ));

        $fieldset->addField('busisness_address', 'label', array(
            'name' => 'busisness_address',
            'label' => $helper->__('Business Address'),
            'required' => false
        ));

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
            'name' => 'groups[general][store_information][fields][merchant_country][value]',
            'label' => $helper->__('Country'),
            'required' => true,
            'values' => $countries,
            'value' => $addressData['country_id']
        ));

        $countryId = isset($addressData['country_id']) ? $addressData['country_id'] : 'US';
        $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($countryId);

        $regions = $regionCollection->toOptionArray();
        if ($regions) {
            $regions[0]['label'] = '*';
        } else {
            $regions = array(array('value' => '', 'label' => '*'));
        }
        $regionHelper = $this->helper('Mage_Directory_Helper_Data');
        $fieldset->addField('region_id', 'select', array(
            'name' => 'region_id',
            'label' => $helper->__('State/Region'),
            'values' => $regions,
            'value' => $addressData['region_id'],
            'after_element_html' => '<script type="text/javascript">'
                . 'var updater = new RegionUpdater("country_id", "", "region_id", '
                . $regionHelper->getRegionJson() . ', "disable");'
                . 'updater.update();'
                . '</script>',
        ));

        $fieldset->addField('vat_number', 'text', array(
            'name' => 'groups[general][store_information][fields][merchant_vat_number][value]',
            'label' => $helper->__('VAT Number (United Kingdom only)'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('general/store_information/merchant_vat_number')
        ));

        $fieldset->addField('validate_vat_number', 'button', array(
            'name' => 'validate_vat_number',
            'required' => false,
            'value' => $helper->__('Validate VAT Number')
        ));

        // Set custom renderer for VAT field
        $vatIdElement = $form->getElement('validate_vat_number');
        $this->_validateVatBlock->setMerchantCountryField('country_id');
        $this->_validateVatBlock->setMerchantVatNumberField('vat_number');
        $vatIdElement->setRenderer($this->_validateVatBlock);

        $fieldset->addField('use_for_shipping', 'checkbox', array(
            'name' => 'use_for_shipping',
            'label' => $helper->__('Use this address as the point of origin for shipping'),
            'required' => false,
            'value' => 0,
            'checked' => $addressData['use_for_shipping']
        ));

        $fieldset->setAdvancedLabel($helper->__('Add Store Email Addresses'));

        $fieldset->addField('sales_representative', 'label', array(
            'name' => 'sales_representative',
            'label' => $helper->__('Sales Representative'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_representative', 'text', array(
            'name' => 'groups[trans_email][ident_sales][fields][name][value]',
            'label' => $helper->__('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_sales/name')
        ), false, true);

        $fieldset->addField('sender_email_representative', 'text', array(
            'name' => 'groups[trans_email][ident_sales][fields][email][value]',
            'label' => $helper->__('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_sales/email')
        ), false, true);

        $fieldset->addField('customer_support', 'label', array(
            'name' => 'customer_support',
            'label' => $helper->__('Customer Support'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_support', 'text', array(
            'name' => 'groups[trans_email][ident_support][fields][name][value]',
            'label' => $helper->__('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_support/name')
        ), false, true);

        $fieldset->addField('sender_email_support', 'text', array(
            'name' => 'groups[trans_email][ident_support][fields][email][value]',
            'label' => $helper->__('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_support/email')
        ), false, true);

        $fieldset->addField('custom_email1', 'label', array(
            'name' => 'custom_email1',
            'label' => $helper->__('Custom Email 1'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_custom1', 'text', array(
            'name' => 'groups[trans_email][ident_custom1][fields][name][value]',
            'label' => $helper->__('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom1/name')
        ), false, true);

        $fieldset->addField('sender_email_custom1', 'text', array(
            'name' => 'groups[trans_email][ident_custom1][fields][email][value]',
            'label' => $helper->__('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom1/email')
        ), false, true);

        $fieldset->addField('custom_email2', 'label', array(
            'name' => 'custom_email2',
            'label' => $helper->__('Custom Email 2'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_custom2', 'text', array(
            'name' => 'groups[trans_email][ident_custom2][fields][name][value]',
            'label' => $helper->__('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom2/name')
        ), false, true);

        $fieldset->addField('sender_email_custom2', 'text', array(
            'name' => 'groups[trans_email][ident_custom2][fields][email][value]',
            'label' => $helper->__('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom2/email')
        ), false, true);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get address data from system configuration
     *
     * @todo This function will be refactored when System->Configuration->General->Store Information
     * "Store Contact Address" format is changed
     *
     * @return array
     */
    public function getAddressData()
    {
        $addressData = array(
            'street_line1' => '',
            'street_line2' => '',
            'city' => '',
            'postcode' => '',
            'region_id' => ''
        );
        $useForShipping = false;

        $address = $this->_storeConfig->getConfig('general/store_information/address');
        $addressValues = explode("\n", $address);
        $addressPresent = count($addressValues) == 5;

        if ($addressPresent) {
            $addressData = array_combine(array_keys($addressData), $addressValues);
        }

        $addressData['country_id'] = $this->_storeConfig->getConfig('general/store_information/merchant_country');

        if ($addressPresent) {
            $regionId = $this->_regionModel
                ->loadByName($addressData['region_id'], $addressData['country_id'])
                ->getRegionId();
            $addressData['region_id'] = !empty($regionId) ? $regionId : 0;

            $useForShipping = true;

            foreach ($addressData as $key => $val) {
                if ($val != $this->_storeConfig->getConfig('shipping/origin/' . $key)) {
                    $useForShipping = false;
                    break;
                }
            }
        }

        $addressData['use_for_shipping'] = $useForShipping;

        return $addressData;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Store Info');
    }
}
