<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer addresses forms
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_Addresses extends Magento_Backend_Block_Widget_Form_Generic
{
    protected $_template = 'customer/tab/addresses.phtml';

    /**
     * Adminhtml addresses
     *
     * @var Magento_Adminhtml_Helper_Addresses
     */
    protected $_adminhtmlAddresses = null;

    /**
     * @param Magento_Adminhtml_Model_Customer_Renderer_RegionFactory $regionFactory
     * @param Magento_Customer_Model_AddressFactory $addressFactory
     * @param Magento_Customer_Model_FormFactory $customerFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Adminhtml_Helper_Addresses $adminhtmlAddresses
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Model_Customer_Renderer_RegionFactory $regionFactory,
        Magento_Customer_Model_AddressFactory $addressFactory,
        Magento_Customer_Model_FormFactory $customerFactory,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Data_Form_Factory $formFactory,
        Magento_Adminhtml_Helper_Addresses $adminhtmlAddresses,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_adminhtmlAddresses = $adminhtmlAddresses;
        $this->_regionFactory = $regionFactory;
        $this->_addressFactory = $addressFactory;
        $this->_customerFactory = $customerFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    public function getRegionsUrl()
    {
        return $this->getUrl('*/json/countryRegion');
    }

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'  => __('Delete Address'),
            'name'   => 'delete_address',
            'element_name' => 'delete_address',
            'disabled' => $this->isReadonly(),
            'class'  => 'delete' . ($this->isReadonly() ? ' disabled' : '')
        ));
        $this->addChild('add_address_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'  => __('Add New Address'),
            'id'     => 'add_address_button',
            'name'   => 'add_address_button',
            'element_name' => 'add_address_button',
            'disabled' => $this->isReadonly(),
            'class'  => 'add'  . ($this->isReadonly() ? ' disabled' : '')
        ));
        $this->addChild('cancel_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'  => __('Cancel'),
            'id'     => 'cancel_add_address'.$this->getTemplatePrefix(),
            'name'   => 'cancel_address',
            'element_name' => 'cancel_address',
            'class'  => 'cancel delete-address'  . ($this->isReadonly() ? ' disabled' : ''),
            'disabled' => $this->isReadonly()
        ));
        return parent::_prepareLayout();
    }

    /**
     * Check block is readonly.
     *
     * @return boolean
     */
    public function isReadonly()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return $customer->isReadonly();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Initialize form object
     *
     * @return Magento_Adminhtml_Block_Customer_Edit_Tab_Addresses
     */
    public function initForm()
    {
        /* @var $customer Magento_Customer_Model_Customer */
        $customer = $this->_coreRegistry->registry('current_customer');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('address_fieldset', array(
            'legend'    => __("Edit Customer's Address"))
        );

        $addressModel = $this->_addressFactory->create();
        $addressModel->setCountryId($this->_coreData->getDefaultCountry($customer->getStore()));
        /** @var $addressForm Magento_Customer_Model_Form */
        $addressForm = $this->_customerFactory->create();
        $addressForm->setFormCode('adminhtml_customer_address')
            ->setEntity($addressModel)
            ->initDefaultValues();

        $attributes = $addressForm->getAttributes();
        if (isset($attributes['street'])) {
            $this->_adminhtmlAddresses
                ->processStreetAttribute($attributes['street']);
        }
        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute */
            $attribute->setFrontendLabel(__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }
        $this->_setFieldset($attributes, $fieldset);

        $regionElement = $form->getElement('region');
        $regionElement->setRequired(true);
        if ($regionElement) {
            $regionElement->setRenderer($this->_regionFactory->create());
        }

        $regionElement = $form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $country = $form->getElement('country_id');
        if ($country) {
            $country->addClass('countries');
        }

        if ($this->isReadonly()) {
            foreach ($addressModel->getAttributes() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $customerStoreId = null;
        if ($customer->getId()) {
            $customerStoreId = $this->_storeManager->getWebsite($customer->getWebsiteId())
                ->getDefaultStore()
                ->getId();
        }

        $prefixElement = $form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->helper('Magento_Customer_Helper_Data')->getNamePrefixOptions($customerStoreId);
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField($prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    '^'
                );
                $prefixField->setValues($prefixOptions);
            }
        }

        $suffixElement = $form->getElement('suffix');
        if ($suffixElement) {
            $suffixOptions = $this->helper('Magento_Customer_Helper_Data')->getNameSuffixOptions($customerStoreId);
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField($suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
            }
        }

        $addressCollection = $customer->getAddresses();
        $this->assign('customer', $customer);
        $this->assign('addressCollection', $addressCollection);
        $form->setValues($addressModel->getData());
        $this->setForm($form);

        return $this;
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('cancel_button');
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_address_button');
    }

    public function getTemplatePrefix()
    {
        return '_template_';
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => 'Magento_Adminhtml_Block_Customer_Form_Element_File',
            'image'     => 'Magento_Adminhtml_Block_Customer_Form_Element_Image',
            'boolean'   => 'Magento_Adminhtml_Block_Customer_Form_Element_Boolean',
        );
    }

    /**
     * Return JSON object with countries associated to possible websites
     *
     * @return string
     */
    public function getDefaultCountriesJson()
    {
        $websites = $this->_systemStore->getWebsiteValuesForForm(false, true);
        $result = array();
        foreach ($websites as $website) {
            $result[$website['value']] = $this->_storeManager->getWebsite($website['value'])
                ->getConfig(
                    Magento_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY
                );
        }

        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Add specified values to name prefix element values
     *
     * @param string|int|array $values
     * @return Magento_Adminhtml_Block_Customer_Edit_Tab_Addresses
     */
    public function addValuesToNamePrefixElement($values)
    {
        if ($this->getForm() && $this->getForm()->getElement('prefix')) {
            $this->getForm()->getElement('prefix')->addElementValues($values);
        }
        return $this;
    }

    /**
     * Add specified values to name suffix element values
     *
     * @param string|int|array $values
     * @return Magento_Adminhtml_Block_Customer_Edit_Tab_Addresses
     */
    public function addValuesToNameSuffixElement($values)
    {
        if ($this->getForm() && $this->getForm()->getElement('suffix')) {
            $this->getForm()->getElement('suffix')->addElementValues($values);
        }
        return $this;
    }
}
