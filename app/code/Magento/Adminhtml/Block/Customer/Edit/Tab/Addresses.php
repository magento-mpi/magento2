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
namespace Magento\Adminhtml\Block\Customer\Edit\Tab;

class Addresses extends \Magento\Adminhtml\Block\Widget\Form
{
    protected $_template = 'customer/tab/addresses.phtml';

    public function getRegionsUrl()
    {
        return $this->getUrl('*/json/countryRegion');
    }

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'  => __('Delete Address'),
            'name'   => 'delete_address',
            'element_name' => 'delete_address',
            'disabled' => $this->isReadonly(),
            'class'  => 'delete' . ($this->isReadonly() ? ' disabled' : '')
        ));
        $this->addChild('add_address_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'  => __('Add New Address'),
            'id'     => 'add_address_button',
            'name'   => 'add_address_button',
            'element_name' => 'add_address_button',
            'disabled' => $this->isReadonly(),
            'class'  => 'add'  . ($this->isReadonly() ? ' disabled' : '')
        ));
        $this->addChild('cancel_button', '\Magento\Adminhtml\Block\Widget\Button', array(
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
        $customer = \Mage::registry('current_customer');
        return $customer->isReadonly();
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Initialize form object
     *
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\Addresses
     */
    public function initForm()
    {
        /* @var $customer \Magento\Customer\Model\Customer */
        $customer = \Mage::registry('current_customer');

        $form = new \Magento\Data\Form();
        $fieldset = $form->addFieldset('address_fieldset', array(
            'legend'    => __("Edit Customer's Address"))
        );

        $addressModel = \Mage::getModel('\Magento\Customer\Model\Address');
        $addressModel->setCountryId(\Mage::helper('Magento\Core\Helper\Data')->getDefaultCountry($customer->getStore()));
        /** @var $addressForm \Magento\Customer\Model\Form */
        $addressForm = \Mage::getModel('\Magento\Customer\Model\Form');
        $addressForm->setFormCode('adminhtml_customer_address')
            ->setEntity($addressModel)
            ->initDefaultValues();

        $attributes = $addressForm->getAttributes();
        if(isset($attributes['street'])) {
            \Mage::helper('Magento\Adminhtml\Helper\Addresses')
                ->processStreetAttribute($attributes['street']);
        }
        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $attribute->setFrontendLabel(__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }
        $this->_setFieldset($attributes, $fieldset);

        $regionElement = $form->getElement('region');
        $regionElement->setRequired(true);
        if ($regionElement) {
            $regionElement->setRenderer(\Mage::getModel('\Magento\Adminhtml\Model\Customer\Renderer\Region'));
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
            $customerStoreId = \Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
        }

        $prefixElement = $form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->helper('\Magento\Customer\Helper\Data')->getNamePrefixOptions($customerStoreId);
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
            $suffixOptions = $this->helper('\Magento\Customer\Helper\Data')->getNameSuffixOptions($customerStoreId);
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
            'file'      => '\Magento\Adminhtml\Block\Customer\Form\Element\File',
            'image'     => '\Magento\Adminhtml\Block\Customer\Form\Element\Image',
            'boolean'   => '\Magento\Adminhtml\Block\Customer\Form\Element\Boolean',
        );
    }

    /**
     * Return JSON object with countries associated to possible websites
     *
     * @return string
     */
    public function getDefaultCountriesJson() {
        $websites = \Mage::getSingleton('Magento\Core\Model\System\Store')->getWebsiteValuesForForm(false, true);
        $result = array();
        foreach ($websites as $website) {
            $result[$website['value']] = \Mage::app()->getWebsite($website['value'])->getConfig(
                \Magento\Core\Helper\Data::XML_PATH_DEFAULT_COUNTRY
            );
        }

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result);
    }

    /**
     * Add specified values to name prefix element values
     *
     * @param string|int|array $values
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\Addresses
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
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\Addresses
     */
    public function addValuesToNameSuffixElement($values)
    {
        if ($this->getForm() && $this->getForm()->getElement('suffix')) {
            $this->getForm()->getElement('suffix')->addElementValues($values);
        }
        return $this;
    }
}
