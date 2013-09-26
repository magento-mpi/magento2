<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost form block
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Authorizenet_Block_Directpost_Form extends Magento_Payment_Block_Form_Cc
{
    protected $_template = 'directpost/info.phtml';

    /**
     * @var Magento_Authorizenet_Model_Directpost
     */
    protected $_model;

    /**
     * @var Magento_Checkout_Model_Type_Onepage
     */
    protected $_checkoutModel;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Payment_Model_Config $paymentConfig
     * @param Magento_Authorizenet_Model_Directpost $model
     * @param Magento_Checkout_Model_Type_Onepage $checkoutModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Payment_Model_Config $paymentConfig,
        Magento_Authorizenet_Model_Directpost $model,
        Magento_Checkout_Model_Type_Onepage $checkoutModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $paymentConfig, $data);
        $this->_model = $model;
        $this->_checkoutModel = $checkoutModel;
    }


    /**
     * Render block HTML
     * If method is not directpost - nothing to return
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getMethod()->getCode() == $this->_model->getCode() ? parent::_toHtml() : '';
    }

    /**
     * Set method info
     *
     * @return Magento_Authorizenet_Block_Directpost_Form
     */
    public function setMethodInfo()
    {
        $payment = $this->_checkoutModel->getQuote()->getPayment();
        $this->setMethod($payment->getMethodInstance());
        return $this;
    }

    /**
     * Get type of request
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this
            ->getRequest()
            ->getParam('isAjax');
    }
}
