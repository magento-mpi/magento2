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
 * Adminhtml sales order edit address block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Address_Form
    extends Magento_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    protected $_template = 'sales/order/address/form.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Customer_Model_AddressFactory $addressFactory
     * @param Magento_Customer_Model_FormFactory $customerFormFactory
     * @param Magento_Adminhtml_Helper_Addresses $adminhtmlAddresses
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Customer_Model_AddressFactory $addressFactory,
        Magento_Customer_Model_FormFactory $customerFormFactory,
        Magento_Adminhtml_Helper_Addresses $adminhtmlAddresses,
        Magento_Data_Form_Factory $formFactory,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $addressFactory, $customerFormFactory, $adminhtmlAddresses, $formFactory,
            $sessionQuote, $orderCreate, $coreData, $context, $data
        );
    }

    /**
     * Order address getter
     *
     * @return Magento_Sales_Model_Order_Address
     */
    protected function _getAddress()
    {
        return $this->_coreRegistry->registry('order_address');
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Billing_Address
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction($this->getUrl('*/*/addressSave', array('address_id'=>$this->_getAddress()->getId())));
        $this->_form->setUseContainer(true);
        return $this;
    }

    /**
     * Form header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Order Address Information');
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_getAddress()->getData();
    }
}
