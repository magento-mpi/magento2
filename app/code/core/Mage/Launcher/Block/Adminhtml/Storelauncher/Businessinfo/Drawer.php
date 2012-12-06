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
     * Region Helper
     *
     * @var Mage_Directory_Helper_Data
     */
    protected $_regionHelper;

    /**
     * Launcher Data helper
     *
     * @var Mage_Launcher_Helper_Data
     */
    protected $_helper;

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
        Mage_Directory_Model_Config_Source_Country $countryModel,
        Mage_Directory_Model_Region $regionModel,
        Mage_Launcher_Helper_Data $dataHelper,
        Mage_Directory_Helper_Data $regionHelper,
        Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory $validateVat,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
        $this->_regionHelper = $regionHelper;
        $this->_helper = $dataHelper;
        $this->_validateVatBlock = $validateVat->createVatValidator();
    }

    /**
     * Prepare Bussinessinfo drawer form
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->_helper->__('Store Info')));
        $fieldset->addField('store_name', 'text', array(
            'name' => 'store_name',
            'label' => $this->_helper->__('Store Name'),
            'required' => false
        ));

        $fieldset->addField('store_email', 'text', array(
            'name' => 'store_email',
            'label' => $this->_helper->__('Store Contact Email'),
            'required' => false
        ));

        $fieldset->addField('store_phone', 'text', array(
            'name' => 'store_phone',
            'label' => $this->_helper->__('Store Contact Phone Number'),
            'required' => false
        ));

        $fieldset->addField('busisness_address', 'label', array(
            'name' => 'busisness_address',
            'label' => $this->_helper->__('Business Address'),
            'required' => false
        ));

        $fieldset->addField('streen_address_1', 'text', array(
            'name' => 'streen_address_1',
            'label' => $this->_helper->__('Street Address 1'),
            'required' => false
        ));

        $fieldset->addField('streen_address_2', 'text', array(
            'name' => 'streen_address_2',
            'label' => $this->_helper->__('Street Address 2'),
            'required' => false
        ));

        $fieldset->addField('city', 'text', array(
            'name' => 'city',
            'label' => $this->_helper->__('City'),
            'required' => false
        ));

        $fieldset->addField('zip_code', 'text', array(
            'name' => 'zip_code',
            'label' => $this->_helper->__('ZIP/Postal Code'),
            'required' => false
        ));

        $countries = $this->_countryModel->toOptionArray();
        $fieldset->addField('country_id', 'select', array(
            'name' => 'country_id',
            'label' => $this->_helper->__('Country'),
            'required' => true,
            'values' => $countries
        ));

        $regionCollection = $this->_regionModel->getCollection()->addCountryFilter('US');

        $regions = $regionCollection->toOptionArray();
        if ($regions) {
            $regions[0]['label'] = '*';
        } else {
            $regions = array(array('value' => '', 'label' => '*'));
        }
        $fieldset->addField('region_id', 'select', array(
            'name' => 'region_id',
            'label' => $this->_helper->__('State/Region'),
            'values' => $regions,
            'after_element_html' => '<script type="text/javascript">'
                . 'var updater = new RegionUpdater("country_id", "", "region_id", '
                .  $this->_regionHelper->getRegionJson() . ', "disable");'
                . 'updater.update();'
                . '</script>',
        ));

        $fieldset->addField('vat_number', 'text', array(
            'name' => 'vat_number',
            'label' => $this->_helper->__('VAT Number (United Kingdom only)'),
            'required' => false
        ));

        $fieldset->addField('validate_vat_number', 'button', array(
            'name' => 'validate_vat_number',
            'label' => $this->_helper->__('Validate VAT Number'),
            'required' => false,
            'value' => $this->_helper->__('Validate VAT Number')
        ));

        // Set custom renderer for VAT field
        $vatIdElement = $form->getElement('validate_vat_number');
        $this->_validateVatBlock->setMerchantCountryField('country_id');
        $this->_validateVatBlock->setMerchantVatNumberField('vat_number');
        $vatIdElement->setRenderer($this->_validateVatBlock);

        $fieldset->addField('use_for_shipping', 'checkbox', array(
            'name' => 'use_for_shipping',
            'label' => $this->_helper->__('Use this address as the point of origin for shipping'),
            'required' => false
        ));

        $fieldset->setAdvancedLabel($this->_helper->__('Add Store Email Addresses'));

        $fieldset->addField('general_contact', 'label', array(
            'name' => 'general_contact',
            'label' => $this->_helper->__('General Contact'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_general', 'text', array(
            'name' => 'sender_name_general',
            'label' => $this->_helper->__('Sender Name'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_email_general', 'text', array(
            'name' => 'sender_email_general',
            'label' => $this->_helper->__('Sender Email'),
            'required' => false
        ), false, true);

        $fieldset->addField('sales_representative', 'label', array(
            'name' => 'sales_representative',
            'label' => $this->_helper->__('Sales Representative'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_representative', 'text', array(
            'name' => 'sender_name_representative',
            'label' => $this->_helper->__('Sender Name'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_email_representative', 'text', array(
            'name' => 'sender_email_representative',
            'label' => $this->_helper->__('Sender Email'),
            'required' => false
        ), false, true);

        $fieldset->addField('customer_support', 'label', array(
            'name' => 'customer_support',
            'label' => $this->_helper->__('Customer Support'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_name_support_1', 'text', array(
            'name' => 'sender_name_support_1',
            'label' => $this->_helper->__('Sender Name'),
            'required' => false
        ), false, true);

        $fieldset->addField('sender_email_support_1', 'text', array(
            'name' => 'sender_email_support_1',
            'label' => $this->_helper->__('Sender Email'),
            'required' => false
        ), false, true);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
