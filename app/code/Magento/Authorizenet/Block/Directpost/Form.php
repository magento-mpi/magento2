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
namespace Magento\Authorizenet\Block\Directpost;

class Form extends \Magento\Payment\Block\Form\Cc
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

    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Helper_Data $coreData,
        Magento_Authorizenet_Model_Directpost $model,
        Magento_Checkout_Model_Type_Onepage $checkoutModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
     * @return \Magento\Authorizenet\Block\Directpost\Form
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
