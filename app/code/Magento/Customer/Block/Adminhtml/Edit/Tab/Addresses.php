<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\Dto\Eav\AttributeMetadataBuilder;
use Magento\Customer\Service\V1\Dto\Address;
use Magento\Exception\NoSuchEntityException;

/**
 * Customer addresses forms
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Addresses extends GenericMetadata
{
    /** Default street line count */
    const DEFAULT_STREET_LINES_COUNT = 2;

    protected $_template = 'tab/addresses.phtml';

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;
    
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;
    
    /** @var \Magento\Customer\Helper\Address */
    protected $_addressHelper;

    /** @var  \Magento\Customer\Model\Metadata\FormFactory */
    protected $_metadataFormFactory;

    /** @var  \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $_customerService;

    /** @var  \Magento\Customer\Service\V1\CustomerMetadataServiceInterface */
    protected $_metadataService;

    /** @var  \Magento\Customer\Service\V1\Dto\AddressBuilder */
    protected $_addressBuilder;

    /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder */
    protected $_customerBuilder;

    /** @var  AttributeMetadataBuilder */
    protected $_attributeMetadataBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\System\Store $systemStore,
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Renderer\RegionFactory $regionFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService
     * @param \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param AttributeMetadataBuilder $attributeMetadataBuilder;
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\System\Store $systemStore,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Renderer\RegionFactory $regionFactory,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        AttributeMetadataBuilder $attributeMetadataBuilder,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
        $this->_customerHelper = $customerHelper;
        $this->_addressHelper = $addressHelper;
        $this->_coreData = $coreData;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_regionFactory = $regionFactory;
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->_systemStore = $systemStore;
        $this->_customerService = $customerService;
        $this->_metadataService = $metadataService;
        $this->_addressBuilder = $addressBuilder;
        $this->_customerBuilder = $customerBuilder;
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
        $this->_directoryHelper = $directoryHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getRegionsUrl()
    {
        return $this->getUrl('directory/json/countryRegion');
    }

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Magento\Backend\Block\Widget\Button', array(
                'label'  => __('Delete Address'),
                'name'   => 'delete_address',
                'element_name' => 'delete_address',
                'disabled' => $this->isReadonly(),
                'class'  => 'delete' . ($this->isReadonly() ? ' disabled' : '')
            ));
        $this->addChild('add_address_button', 'Magento\Backend\Block\Widget\Button', array(
                'label'  => __('Add New Address'),
                'id'     => 'add_address_button',
                'name'   => 'add_address_button',
                'element_name' => 'add_address_button',
                'disabled' => $this->isReadonly(),
                'class'  => 'add'  . ($this->isReadonly() ? ' disabled' : '')
            ));
        $this->addChild('cancel_button', 'Magento\Backend\Block\Widget\Button', array(
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
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        if (empty($customerId)) {
            return false;
        }

        try {
            return $this->_customerService->isReadonly($customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Initialize form object
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function initForm()
    {

        $customerData = $this->_backendSession->getCustomerData();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('address_fieldset', array(
                'legend'    => __("Edit Customer's Address"))
        );

        $account = $customerData['account'];
        $this->_addressBuilder->populateWithArray([]);
        if (!empty($account) && isset($account['store_id'])) {
            $this->_addressBuilder->setCountryId(
                $this->_coreData->getDefaultCountry(
                    $this->_storeManager->getStore($account['store_id'])
                )
            );
        } else {
            $this->_addressBuilder->setCountryId($this->_coreData->getDefaultCountry());
        }
        $address = $this->_addressBuilder->create();

        $addressForm = $this->_metadataFormFactory->create(
            'customer_address',
            'adminhtml_customer_address',
            $address->getAttributes()
        );

        $attributes = $addressForm->getAttributes();
        if (isset($attributes['street'])) {
            if ($attributes['street']->getMultilineCount() <= 0 ) {
                $attributes['street'] = $this->_attributeMetadataBuilder->populate($attributes['street'])
                    ->setMultilineCount(self::DEFAULT_STREET_LINES_COUNT)
                    ->create();
            }
        }
        foreach ($attributes as $key => $attribute) {
            $attributes[$key] = $this->_attributeMetadataBuilder->populate($attribute)
                ->setFrontendLabel(__($attribute->getFrontendLabel()))
                ->setVisible(false)
                ->create();
        }
        $this->_setFieldset($attributes, $fieldset);

        $regionElement = $form->getElement('region');

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
            foreach ($this->_metadataService->getAllAddressAttributeMetadata() as $attribute) {
                $element = $form->getElement($attribute->getAttributeCode());
                if ($element) {
                    $element->setReadonly(true, true);
                }
            }
        }

        $customerStoreId = null;
        if (!empty($account) && isset($account['id']) && isset($account['website_id'])) {
            $customerStoreId = $this->_storeManager->getWebsite($account['website_id'])
                ->getDefaultStore()
                ->getId();
        }

        $prefixElement = $form->getElement('prefix');
        if ($prefixElement) {
            $prefixOptions = $this->_customerHelper->getNamePrefixOptions($customerStoreId);
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
            $suffixOptions = $this->_customerHelper->getNameSuffixOptions($customerStoreId);
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

        $this->assign('customer', $this->_customerBuilder->populateWithArray($account)->create());
        $addressCollection = [];
        foreach ($customerData['address'] as $key => $addressData) {
            $addressCollection[$key] = $this->_addressBuilder->populateWithArray($addressData)
                ->create();
        }
        $this->assign('addressCollection', $addressCollection);
        $form->setValues($address->getAttributes());
        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('cancel_button');
    }

    /**
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_address_button');
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => 'Magento\Customer\Block\Adminhtml\Form\Element\File',
            'image'     => 'Magento\Customer\Block\Adminhtml\Form\Element\Image',
            'boolean'   => 'Magento\Customer\Block\Adminhtml\Form\Element\Boolean',
        );
    }

    /**
     * Add specified values to name prefix element values
     *
     * @param string|int|array $values
     * @return $this
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
     * @return $this
     */
    public function addValuesToNameSuffixElement($values)
    {
        if ($this->getForm() && $this->getForm()->getElement('suffix')) {
            $this->getForm()->getElement('suffix')->addElementValues($values);
        }
        return $this;
    }

    /**
     * Returns the template prefix
     *
     * @return string
     */
    public function getTemplatePrefix()
    {
        return '_template_';
    }

    /**
     * Return array with countries associated to possible websites
     *
     * @return array
     */
    public function getDefaultCountries()
    {
        $websites = $this->_systemStore->getWebsiteValuesForForm(false, true);
        $result = array();
        foreach ($websites as $website) {
            $result[$website['value']] = $this->_storeManager->getWebsite($website['value'])
                ->getConfig(
                    \Magento\Core\Helper\Data::XML_PATH_DEFAULT_COUNTRY
                );
        }

        return $result;
    }

    /**
     * Return ISO2 country codes, which have optional Zip/Postal pre-configured
     *
     * @return array
     */
    public function getOptionalZipCountries()
    {
        return $this->_directoryHelper->getCountriesWithOptionalZip();
    }

    /**
     * Returns the list of countries, for which region is required
     *
     * @return array
     */
    public function getRequiredStateForCountries()
    {
        return $this->_directoryHelper->getCountriesWithStatesRequired();
    }

    /**
     * eturn, whether non-required state should be shown
     *
     * @return int 1 if should be shown, and 0 if not.
     */
    public function getShowAllRegions()
    {
        return (string)$this->_directoryHelper->isShowNonRequiredState() ? 1 : 0;
    }

    /**
     * Encode the $data into JSON format.
     *
     * @param object|array $data
     * @return string
     */
    public function jsonEncode($data)
    {
        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Format the given address to the given type
     *
     * @param Address $address
     * @param string $type
     * @return string
     */
    public function format(Address $address, $type)
    {
        return $this->_addressHelper->getFormatTypeRenderer($type)->renderArray($address->getAttributes());
    }
}
