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

class Account extends \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Model\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\FormFactory $customerFormFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\FormFactory $customerFormFactory,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerFormFactory = $customerFormFactory;
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
        /* @var $customerModel \Magento\Customer\Model\Customer */
        $customerModel = $this->_customerFactory->create();

        /* @var $customerForm \Magento\Customer\Model\Form */
        $customerForm   = $this->_customerFormFactory->create();
        $customerForm->setFormCode('adminhtml_checkout')
            ->setStore($this->getStore())
            ->setEntity($customerModel);

        // prepare customer attributes to show
        $attributes     = array();

        // add system required attributes
        foreach ($customerForm->getSystemAttributes() as $attribute) {
            /* @var $attribute \Magento\Customer\Model\Attribute */
            if ($attribute->getIsRequired()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($attributes['group_id']);
        }

        // add user defined attributes
        foreach ($customerForm->getUserAttributes() as $attribute) {
            /* @var $attribute \Magento\Customer\Model\Attribute */
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
     * @return \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm
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
        $data = $this->getCustomer()->getData();
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
