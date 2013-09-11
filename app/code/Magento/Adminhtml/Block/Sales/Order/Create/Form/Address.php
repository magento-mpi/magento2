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
 * Order create address form
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Form;

class Address
    extends \Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm
{
    /**
     * Customer Address Form instance
     *
     * @var \Magento\Customer\Model\Form
     */
    protected $_addressForm;

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_storeConfig->getConfig($path);
    }
    /**
     * Return Customer Address Collection as array
     *
     * @return array
     */
    public function getAddressCollection()
    {
        return $this->getCustomer()->getAddresses();
    }

    /**
     * Return customer address form instance
     *
     * @return \Magento\Customer\Model\Form
     */
    protected function _getAddressForm()
    {
        if (is_null($this->_addressForm)) {
            $this->_addressForm = \Mage::getModel('\Magento\Customer\Model\Form')
                ->setFormCode('adminhtml_customer_address')
                ->setStore($this->getStore());
        }
        return $this->_addressForm;
    }

    /**
     * Return Customer Address Collection as JSON
     *
     * @return string
     */
    public function getAddressCollectionJson()
    {
        $addressForm = $this->_getAddressForm();
        $data = array();

        $emptyAddress = $this->getCustomer()
            ->getAddressById(null)
            ->setCountryId(\Mage::helper('Magento\Core\Helper\Data')->getDefaultCountry($this->getStore()));
        $data[0] = $addressForm->setEntity($emptyAddress)
            ->outputData(\Magento\Customer\Model\Attribute\Data::OUTPUT_FORMAT_JSON);

        foreach ($this->getAddressCollection() as $address) {
            $addressForm->setEntity($address);
            $data[$address->getId()] = $addressForm->outputData(
                \Magento\Customer\Model\Attribute\Data::OUTPUT_FORMAT_JSON
            );
        }
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($data);
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\Create\Form\Address
     */
    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('main', array(
            'no_container' => true
        ));

        /* @var $addressModel \Magento\Customer\Model\Address */
        $addressModel = \Mage::getModel('\Magento\Customer\Model\Address');

        $addressForm = $this->_getAddressForm()
            ->setEntity($addressModel);

        $attributes = $addressForm->getAttributes();
        if(isset($attributes['street'])) {
            \Mage::helper('Magento\Adminhtml\Helper\Addresses')
                ->processStreetAttribute($attributes['street']);
        }
        $this->_addAttributesToForm($attributes, $fieldset);

        $prefixElement = $this->_form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->helper('\Magento\Customer\Helper\Data')->getNamePrefixOptions($this->getStore());
            if (!empty($prefixOptions)) {
                $fieldset->removeField($prefixElement->getId());
                $prefixField = $fieldset->addField($prefixElement->getId(),
                    'select',
                    $prefixElement->getData(),
                    '^'
                );
                $prefixField->setValues($prefixOptions);
                if ($this->getAddressId()) {
                    $prefixField->addElementValues($this->getAddress()->getPrefix());
                }
            }
        }

        $suffixElement = $this->_form->getElement('suffix');
        if ($suffixElement) {
            $suffixOptions = $this->helper('\Magento\Customer\Helper\Data')->getNameSuffixOptions($this->getStore());
            if (!empty($suffixOptions)) {
                $fieldset->removeField($suffixElement->getId());
                $suffixField = $fieldset->addField($suffixElement->getId(),
                    'select',
                    $suffixElement->getData(),
                    $this->_form->getElement('lastname')->getId()
                );
                $suffixField->setValues($suffixOptions);
                if ($this->getAddressId()) {
                    $suffixField->addElementValues($this->getAddress()->getSuffix());
                }
            }
        }


        $regionElement = $this->_form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $this->_form->setValues($this->getFormValues());

        if ($this->_form->getElement('country_id')->getValue()) {
            $countryId = $this->_form->getElement('country_id')->getValue();
            $this->_form->getElement('country_id')->setValue(null);
            foreach ($this->_form->getElement('country_id')->getValues() as $country) {
                if ($country['value'] == $countryId) {
                    $this->_form->getElement('country_id')->setValue($countryId);
                }
            }
        }
        if (is_null($this->_form->getElement('country_id')->getValue())) {
            $this->_form->getElement('country_id')->setValue(
                \Mage::helper('Magento\Core\Helper\Data')->getDefaultCountry($this->getStore())
            );
        }

        // Set custom renderer for VAT field if needed
        $vatIdElement = $this->_form->getElement('vat_id');
        if ($vatIdElement && $this->getDisplayVatValidationButton() !== false) {
            $vatIdElement->setRenderer(
                $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Customer\Sales\Order\Address\Form\Renderer\Vat')
                    ->setJsVariablePrefix($this->getJsVariablePrefix())
            );
        }

        return $this;
    }

    /**
     * Add additional data to form element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return \Magento\Adminhtml\Block\Sales\Order\Create\Form\AbstractForm
     */
    protected function _addAdditionalFormElementData(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getId() == 'region_id') {
            $element->setNoDisplay(true);
        }
        return $this;
    }

    /**
     * Return customer address id
     *
     * @return int|boolean
     */
    public function getAddressId()
    {
        return false;
    }

    /**
     * Return customer address formated as one-line string
     *
     * @param \Magento\Customer\Model\Address $address
     * @return string
     */
    public function getAddressAsString($address)
    {
        return $this->escapeHtml($address->format('oneline'));
    }
}
