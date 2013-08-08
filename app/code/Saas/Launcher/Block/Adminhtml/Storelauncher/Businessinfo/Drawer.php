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
 * Businessinfo Drawer Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Businessinfo_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
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
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Directory_Model_Config_Source_Country $countryModel
     * @param Mage_Directory_Model_Region $regionModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        Mage_Directory_Model_Config_Source_Country $countryModel,
        Mage_Directory_Model_Region $regionModel,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
    }

    /**
     * Prepare Bussinessinfo drawer form
     *
     * @return Mage_Backend_Block_Widget_Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $addressData = $this->getAddressData();

        $form = new Varien_Data_Form(array(
            'method' => 'post',
            'id' => 'drawer-form',
            'class' => 'store-info-form',
        ));

        $helper = $this->helper('Saas_Launcher_Helper_Data');

        $storeInfo = $form->addFieldset('store_info', array(
            'legend' => __('Store Info'),
            'class' => 'fieldset-store-info'
        ));

        $storeInfo->addField('store_name', 'text', array(
            'name' => 'groups[general][store_information][fields][name][value]',
            'label' => __('Store Name'),
            'required' => false,
            'value' => $addressData['name']
        ));

        $storeInfo->addField('store_email', 'text', array(
            'name' => 'groups[trans_email][ident_general][fields][email][value]',
            'label' => __('Store Contact Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $addressData['email']
        ));

        $storeInfo->addField('store_phone', 'text', array(
            'name' => 'groups[general][store_information][fields][phone][value]',
            'label' => __('Store Phone Number'),
            'required' => false,
            'value' => $addressData['phone']
        ));

        $storeInfo->setAdvancedLabel(__('Add Store Email Addresses'));

        $generalContact = $storeInfo->addFieldset('general_contact',
            array('legend' => __('General Contact')), false, true);

        $generalContact->addField('sender_name_general', 'text', array(
            'name' => 'groups[trans_email][ident_general][fields][name][value]',
            'label' => __('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_general/name')
        ));

        $generalContact->addField('sender_email_general', 'text', array(
            'name' => 'groups[trans_email][ident_general][fields][email][value]',
            'label' => __('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'disabled' => 'disabled',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_general/email'),
            'note' => __('Uses Store Contact Email as your General Contact Email.'),
        ));

        $salesRepresentative = $storeInfo->addFieldset('sales_representative',
            array('legend' => __('Sales Representative')), false, true);

        $salesRepresentative->addField('sender_name_representative', 'text', array(
            'name' => 'groups[trans_email][ident_sales][fields][name][value]',
            'label' => __('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_sales/name')
        ));

        $salesRepresentative->addField('sender_email_representative', 'text', array(
            'name' => 'groups[trans_email][ident_sales][fields][email][value]',
            'label' => __('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_sales/email')
        ));

        $customerSupport = $storeInfo->addFieldset('customer_support',
            array('legend' => __('Customer Support')), false, true);

        $customerSupport->addField('sender_name_support', 'text', array(
            'name' => 'groups[trans_email][ident_support][fields][name][value]',
            'label' => __('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_support/name')
        ));

        $customerSupport->addField('sender_email_support', 'text', array(
            'name' => 'groups[trans_email][ident_support][fields][email][value]',
            'label' => __('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_support/email')
        ));

        $customEmail1 = $storeInfo->addFieldset('custom_email1',
            array('legend' => __('Custom Email 1')), false, true);

        $customEmail1->addField('sender_name_custom1', 'text', array(
            'name' => 'groups[trans_email][ident_custom1][fields][name][value]',
            'label' => __('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom1/name')
        ));

        $customEmail1->addField('sender_email_custom1', 'text', array(
            'name' => 'groups[trans_email][ident_custom1][fields][email][value]',
            'label' => __('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom1/email')
        ));

        $customEmail2 = $storeInfo->addFieldset('custom_email2',
            array('legend' => __('Custom Email 2')), false, true);

        $customEmail2->addField('sender_name_custom2', 'text', array(
            'name' => 'groups[trans_email][ident_custom2][fields][name][value]',
            'label' => __('Sender Name'),
            'required' => false,
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom2/name')
        ));

        $customEmail2->addField('sender_email_custom2', 'text', array(
            'name' => 'groups[trans_email][ident_custom2][fields][email][value]',
            'label' => __('Sender Email'),
            'required' => true,
            'class' => 'validate-email',
            'value' => $this->_storeConfig->getConfig('trans_email/ident_custom2/email')
        ));

        $businessAddress = $form->addFieldset('business_address', array(
            'legend' => __('Store Business Address'),
            'class' => 'fieldset-business-address'
        ), false, false);

        $businessAddress->addField('street_line1', 'text', array(
            'name' => 'street_line1',
            'label' => __('Street Address Line 1'),
            'required' => false,
            'value' => $addressData['street_line1']
        ));

        $businessAddress->addField('street_line2', 'text', array(
            'name' => 'street_line2',
            'label' => __('Street Address Line 2'),
            'required' => false,
            'value' => $addressData['street_line2']
        ));

        $businessAddress->addField('city', 'text', array(
            'name' => 'city',
            'label' => __('City'),
            'required' => false,
            'value' => $addressData['city']
        ));

        $businessAddress->addField('postcode', 'text', array(
            'name' => 'postcode',
            'label' => __('ZIP/Postal Code'),
            'required' => false,
            'value' => $addressData['postcode']
        ));

        $isRegionFieldText = true;
        if ($addressData['country_id']) {
            $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($addressData['country_id']);
            $regions = $regionCollection->toOptionArray();
            if (!empty($regions)) {
                $businessAddress->addField('region_id', 'select', array(
                    'name' => 'region_id',
                    'label' => __('State/Region'),
                    'values' => $regions,
                    'value' => $addressData['region_id'],
                ));
                $isRegionFieldText = false;
            }
        }
        if ($isRegionFieldText) {
            $businessAddress->addField('region_id', 'text', array(
                'name' => 'region_id',
                'label' => __('State/Region'),
                'value' => $addressData['region_id']
            ));
        }

        $countries = $this->_countryModel->toOptionArray(false, 'US');
        $businessAddress->addField('country_id', 'select', array(
            'name' => 'groups[general][store_information][fields][country_id][value]',
            'label' => __('Country'),
            'required' => true,
            'values' => $countries,
            'class' => 'countries',
            'value' => $addressData['country_id'],
            'after_element_html' => '<script type="text/javascript">'
                . 'originAddress = new originModel();'
                . '</script>',
        ));

        $businessAddress->addField('use_for_shipping', 'checkbox', array(
            'name' => 'use_for_shipping',
            'label' => __('Use this as my store shipping address'),
            'required' => false,
            'value' => 0,
            'checked' => $addressData['use_for_shipping']
        ));

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
        $addressData = array();
        $addressData['street_line1'] = $this->_storeConfig->getConfig('general/store_information/street_line1');
        $addressData['street_line2'] = $this->_storeConfig->getConfig('general/store_information/street_line2');
        $addressData['city'] = $this->_storeConfig->getConfig('general/store_information/city');
        $addressData['postcode'] = $this->_storeConfig->getConfig('general/store_information/postcode');
        $addressData['country_id'] = $this->_storeConfig->getConfig('general/store_information/country_id');
        $addressData['region_id'] = $this->_storeConfig->getConfig('general/store_information/region_id');

        $addressData['use_for_shipping'] = $this->_isStoreAddressUsedForShipping($addressData);

        $addressData['name'] = $this->_storeConfig->getConfig('general/store_information/name');
        $addressData['phone'] = $this->_storeConfig->getConfig('general/store_information/phone');
        $addressData['email'] = $this->_storeConfig->getConfig('trans_email/ident_general/email');
        return $addressData;
    }

    /**
     * Check if store business address is used for shipping
     *
     * @param array $storeAddressData
     * @return boolean
     */
    protected function _isStoreAddressUsedForShipping(array $storeAddressData)
    {
        // always use business address for shipping if tile is not complete
        if (!$this->getTile()->isComplete()) {
            return true;
        }
        // for complete tile, check if business address is equal to shipping origin
        $useForShipping = true;
        foreach ($storeAddressData as $key => $value) {
            if ($value != $this->_storeConfig->getConfig('shipping/origin/' . $key)) {
                $useForShipping = false;
                break;
            }
        }

        return $useForShipping;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return __('Store Info');
    }
}
