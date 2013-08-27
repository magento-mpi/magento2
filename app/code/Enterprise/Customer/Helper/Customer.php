<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise Customer EAV Attributes Data Helper
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Helper_Customer extends Enterprise_Eav_Helper_Data
{
    /**
     * Data helper
     *
     * @var Enterprise_Customer_Helper_Data $_dataHelper
     */
    protected $_dataHelper;

    /**
     * Input validator
     *
     * @var Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator $_inputValidator
     */
    protected $_inputValidator;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Enterprise_Customer_Helper_Data $dataHelper
     * @param Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator $inputValidator
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Enterprise_Customer_Helper_Data $dataHelper,
        Magento_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator $inputValidator
    ) {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
        $this->_inputValidator = $inputValidator;
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => __('Customer Checkout Register'),
                'value' => 'checkout_register'
            ),
            array(
                'label' => __('Customer Registration'),
                'value' => 'customer_account_create'
            ),
            array(
                'label' => __('Customer Account Edit'),
                'value' => 'customer_account_edit'
            ),
            array(
                'label' => __('Admin Checkout'),
                'value' => 'adminhtml_checkout'
            ),
        );
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @throws Magento_Core_Exception
     * @return array
     */
    public function filterPostData($data)
    {
        $data = parent::filterPostData($data);

        //validate frontend_input
        if (isset($data['frontend_input'])) {
            $this->_inputValidator->setHaystack(
                array_keys($this->_dataHelper->getAttributeInputTypes())
            );
            if (!$this->_inputValidator->isValid($data['frontend_input'])) {
                throw new Magento_Core_Exception($this->stripTags(implode(' ', $this->_inputValidator->getMessages())));
            }
        }
        return $data;
    }
}
