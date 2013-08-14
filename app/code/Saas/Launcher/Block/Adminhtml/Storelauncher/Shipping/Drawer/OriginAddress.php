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
 * Shipping Origin Block
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer_OriginAddress
    extends Magento_Backend_Block_Widget_Form
{
    /**
     * Country Source Model
     *
     * @var Magento_Directory_Model_Config_Source_Country
     */
    protected $_countryConfigModel;

    /**
     * Country Model
     *
     * @var Magento_Directory_Model_Country
     */
    protected $_countryModel;

    /**
     * Regions
     *
     * @var Magento_Directory_Model_Region
     */
    protected $_regionModel;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Directory_Model_Config_Source_Country $countryConfigModel
     * @param Magento_Directory_Model_Country $countryModel
     * @param Magento_Directory_Model_Region $regionModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Directory_Model_Config_Source_Country $countryConfigModel,
        Magento_Directory_Model_Country $countryModel,
        Magento_Directory_Model_Region $regionModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_countryConfigModel = $countryConfigModel;
        $this->_countryModel = $countryModel;
        $this->_regionModel = $regionModel;
    }

    /**
     * Prepare Shipping Origin form
     *
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $addressData = $this->getAddressData();

        $form = new Magento_Data_Form(array(
            'method' => 'post',
            'id' => 'shipping-origin-form'
        ));

        $helper = $this->helper('Saas_Launcher_Helper_Data');

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

        $isRegionFieldText = true;
        if ($addressData['country_id']) {
            $regionCollection = $this->_regionModel->getCollection()->addCountryFilter($addressData['country_id']);
            $regions = $regionCollection->toOptionArray();
            if (!empty($regions)) {
                $fieldset->addField('region_id', 'select', array(
                   'name' => 'region_id',
                   'label' => $helper->__('State/Region'),
                   'values' => $regions,
                   'value' => $addressData['region_id'],
                ));
                $isRegionFieldText = false;
            }
        }

        if ($isRegionFieldText) {
            $fieldset->addField('region_id', 'text', array(
                 'name' => 'region_id',
                 'label' => $helper->__('State/Region'),
                 'value' => $addressData['region_id']
            ));
        }

        $countries = $this->_countryConfigModel->toOptionArray(false, 'US');
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

    /**
     * Get Address
     *
     * @return array
     */
    public function getAddress()
    {
        $addressData = $this->getAddressData();
        $showForm = empty($addressData['street_line1'])
            || empty($addressData['city']) || empty($addressData['postcode']);

        if ($addressData['country_id']) {
            $addressData['country_id'] = $this->_countryModel->loadByCode($addressData['country_id'])->getName();
            if ($addressData['region_id']) {
                $this->_regionModel->load($addressData['region_id']);
                if ($this->_regionModel->getName()) {
                    $addressData['region_id'] = $this->_regionModel->getName();
                }
            }
        }
        return array('show_form' => $showForm, 'data' => $addressData);
    }
}
