<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create order account form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Form;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class Account extends AbstractForm
{
    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /** @var CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_metadataFormFactory = $metadataFormFactory;
        $this->_customerAccountService = $customerAccountService;
        parent::__construct($context, $sessionQuote, $orderCreate, $formFactory, $data);
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-account';
    }

    /**
     * Return header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Account Information');
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $customerForm */
        $customerForm = $this->_metadataFormFactory->create(
            'customer',
            'adminhtml_checkout'
        );

        // prepare customer attributes to show
        $attributes = [];

        // add system required attributes
        foreach ($customerForm->getSystemAttributes() as $attribute) {
            if ($attribute->isRequired()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($attributes['group_id']);
        }

        // add user defined attributes
        foreach ($customerForm->getUserAttributes() as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        $fieldset = $this->_form->addFieldset('main', array());

        $this->_addAttributesToForm($attributes, $fieldset);

        $this->_form->addFieldNameSuffix('order[account]');
        $this->_form->setValues($this->getFormValues());

        return $this;
    }

    /**
     * Add additional data to form element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return AbstractForm
     */
    protected function _addAdditionalFormElementData(\Magento\Data\Form\Element\AbstractElement $element)
    {
        switch ($element->getId()) {
            case 'email':
                $element->setRequired(0);
                $element->setClass('validate-email');
                break;
        }
        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        try {
            $customer = $this->_customerAccountService->getCustomer($this->getCustomerId());
        } catch (\Exception $e) {
            /** If customer does not exist do nothing. */
        }
        $data = isset($customer) ? \Magento\Service\DataObjectConverter::toFlatArray($customer) : [];
        foreach ($this->getQuote()->getData() as $key => $value) {
            if (strpos($key, 'customer_') === 0) {
                $data[substr($key, 9)] = $value;
            }
        }

        if ($this->getQuote()->getCustomerEmail()) {
            $data['email']  = $this->getQuote()->getCustomerEmail();
        }

        return $data;
    }
}
