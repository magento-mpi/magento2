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
     * @var \Magento\Authorizenet\Model\Directpost
     */
    protected $_model;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_checkoutModel;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Authorizenet\Model\Directpost $model
     * @param \Magento\Checkout\Model\Type\Onepage $checkoutModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Authorizenet\Model\Directpost $model,
        \Magento\Checkout\Model\Type\Onepage $checkoutModel,
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
