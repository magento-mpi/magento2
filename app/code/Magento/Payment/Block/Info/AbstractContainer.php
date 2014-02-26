<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Block\Info;

/**
 * Payment information container block
 *
 * @category   Magento
 * @package    Magento_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractContainer extends \Magento\View\Element\Template
{
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($context, $data);
    }

    /**
     * Add payment info block to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($info = $this->getPaymentInfo()) {
            $this->setChild(
                $this->_getInfoBlockName(),
                $this->_paymentData->getInfoBlock($info)
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve info block name
     *
     * @return string|false
     */
    protected function _getInfoBlockName()
    {
        if ($info = $this->getPaymentInfo()) {
            return 'payment.info.'.$info->getMethodInstance()->getCode();
        }
        return false;
    }

    /**
     * Retrieve payment info model
     *
     * @return \Magento\Payment\Model\Info|false
     */
    abstract public function getPaymentInfo();

    /**
     * Declare info block template
     *
     * @param string $method
     * @param string $template
     * @return $this
     */
    public function setInfoTemplate($method='', $template='')
    {
        if ($info = $this->getPaymentInfo()) {
            if ($info->getMethodInstance()->getCode() == $method) {
                $this->getChildBlock($this->_getInfoBlockName())->setTemplate($template);
            }
        }
        return $this;
    }
}
